"""Training pipeline for NutriScreen-ES stunting and malnutrition screening.

This script trains interpretable tabular classifiers and exports Decision Tree
rules that can later be reviewed before being added to the Laravel knowledge base.
"""

from __future__ import annotations

import argparse
import math
import re
from pathlib import Path
from datetime import datetime
from typing import Dict, Iterable, List, Optional, Tuple

import joblib
import pandas as pd
from sklearn.ensemble import RandomForestClassifier
from sklearn.metrics import (
    accuracy_score,
    classification_report,
    confusion_matrix,
    f1_score,
    precision_score,
    recall_score,
)
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import LabelEncoder
from sklearn.tree import DecisionTreeClassifier, export_text


BASE_DIR = Path(__file__).resolve().parent
DEFAULT_DATA_DIR = BASE_DIR / "data"
DEFAULT_ARTIFACTS_DIR = BASE_DIR / "artifacts"
SUPPORTED_EXTENSIONS = {".csv", ".xlsx", ".xls"}

TARGET_CANDIDATES = [
    "nutritional_status",
    "stunting_status",
    "status",
    "label",
    "height_for_age",
    "weight_for_age",
    "weight_for_height",
]

FEATURE_ALIASES = {
    "gender": ["gender", "sex", "jenis_kelamin", "kelamin"],
    "age_months": ["age_months", "age_month", "age_in_months", "usia_bulan", "umur_bulan"],
    "weight_kg": ["weight_kg", "weight", "body_weight_kg", "berat_kg", "berat_badan_kg"],
    "height_cm": ["height_cm", "height", "body_height_cm", "length_cm", "tinggi_cm", "tinggi_badan_cm"],
    "weight_for_age_zscore": [
        "weight_for_age_zscore",
        "z_score_w_a",
        "wfa_zscore",
        "bb_u_zscore",
        "zscore_bb_u",
    ],
    "height_for_age_zscore": [
        "height_for_age_zscore",
        "z_score_h_a",
        "hfa_zscore",
        "tb_u_zscore",
        "zscore_tb_u",
    ],
    "weight_for_height_zscore": [
        "weight_for_height_zscore",
        "z_score_w_h",
        "wfh_zscore",
        "bb_tb_zscore",
        "zscore_bb_tb",
    ],
}


def clean_column_name(column_name: object) -> str:
    """Convert a dataset column name to lowercase snake_case."""
    cleaned = str(column_name).strip().lower()
    cleaned = re.sub(r"[^a-z0-9]+", "_", cleaned)
    cleaned = re.sub(r"_+", "_", cleaned).strip("_")
    return cleaned


def find_dataset_file(data_dir: Path, explicit_file: Optional[Path] = None) -> Path:
    """Find one CSV or Excel dataset in the data directory."""
    if explicit_file:
        dataset_file = explicit_file
        if not dataset_file.is_absolute() and not dataset_file.exists():
            dataset_file = BASE_DIR / explicit_file
        if not dataset_file.exists():
            raise FileNotFoundError(f"Dataset file not found: {dataset_file}")
        if dataset_file.suffix.lower() not in SUPPORTED_EXTENSIONS:
            raise ValueError(f"Unsupported dataset format: {dataset_file.suffix}")
        return dataset_file

    data_files = sorted(
        path for path in data_dir.iterdir() if path.is_file() and path.suffix.lower() in SUPPORTED_EXTENSIONS
    )
    if not data_files:
        raise FileNotFoundError(
            "No dataset file found. Put a .csv, .xlsx, or .xls file in "
            f"{data_dir} or pass --data-file."
        )

    for preferred_name in ["Overall Data.xlsx", "Overall Data.xls", "Preprocessed Data.xlsx", "Preprocessed Data.xls"]:
        preferred_file = data_dir / preferred_name
        if preferred_file in data_files:
            print(f"Using preferred aggregate dataset: {preferred_file.name}")
            return preferred_file

    if len(data_files) > 1:
        print("Multiple dataset files found. Using the first file alphabetically:")
        for path in data_files:
            print(f"- {path.name}")
    return data_files[0]


def load_dataset(dataset_file: Path) -> pd.DataFrame:
    """Load CSV or Excel data into a DataFrame."""
    if dataset_file.suffix.lower() == ".csv":
        return pd.read_csv(dataset_file)
    return pd.read_excel(dataset_file)


def detect_target_column(columns: Iterable[str], explicit_target: Optional[str] = None) -> str:
    """Detect target column from supported target candidate names."""
    available_columns = list(columns)
    if explicit_target:
        target_column = clean_column_name(explicit_target)
        if target_column not in available_columns:
            raise ValueError(
                f"Target column '{target_column}' not found. "
                f"Available columns: {', '.join(available_columns)}"
            )
        return target_column

    for candidate in TARGET_CANDIDATES:
        if candidate in available_columns:
            return candidate

    raise ValueError(
        "Target column not found. Expected one of: "
        f"{', '.join(TARGET_CANDIDATES)}. Available columns: {', '.join(available_columns)}"
    )


def detect_feature_columns(columns: Iterable[str], target_column: str) -> Tuple[List[str], Dict[str, str]]:
    """Detect prioritized feature columns and map source names to canonical names."""
    available_columns = set(columns)
    feature_columns: List[str] = []
    rename_map: Dict[str, str] = {}

    for canonical_name, aliases in FEATURE_ALIASES.items():
        matched_column = next((alias for alias in aliases if alias in available_columns), None)
        if matched_column and matched_column != target_column:
            feature_columns.append(canonical_name)
            if matched_column != canonical_name:
                rename_map[matched_column] = canonical_name

    if not feature_columns:
        raise ValueError(
            "No supported feature columns found. Expected any of: "
            + ", ".join(FEATURE_ALIASES.keys())
        )

    return feature_columns, rename_map


def excel_date_to_decimal(value: object) -> object:
    """Recover decimal values that Excel has interpreted as dates.

    In this dataset, values such as 13.5 may be stored by Excel as 13 May 2025.
    The day is the integer part and the month is the first decimal digit.
    """
    if isinstance(value, (pd.Timestamp, datetime)):
        return float(f"{value.day}.{value.month}")
    return value


def prepare_features(
    df: pd.DataFrame, feature_columns: List[str], target_column: str
) -> Tuple[pd.DataFrame, pd.Series, Dict[str, LabelEncoder], LabelEncoder]:
    """Clean missing values and encode categorical columns."""
    working_df = df[feature_columns + [target_column]].copy()
    working_df = working_df.dropna(subset=[target_column])

    if working_df.empty:
        raise ValueError("Dataset has no rows with a non-empty target label.")

    feature_encoders: Dict[str, LabelEncoder] = {}
    x = working_df[feature_columns].copy()

    for column in feature_columns:
        x[column] = x[column].map(excel_date_to_decimal)
        if pd.api.types.is_numeric_dtype(x[column]):
            x[column] = pd.to_numeric(x[column], errors="coerce")
            fill_value = x[column].median()
            x[column] = x[column].fillna(0 if pd.isna(fill_value) else fill_value)
            continue

        numeric_version = pd.to_numeric(x[column], errors="coerce")
        numeric_ratio = numeric_version.notna().mean()
        if numeric_ratio >= 0.9:
            fill_value = numeric_version.median()
            x[column] = numeric_version.fillna(0 if pd.isna(fill_value) else fill_value)
            continue

        label_encoder = LabelEncoder()
        x[column] = x[column].fillna("missing").astype(str).str.strip().str.lower()
        x[column] = label_encoder.fit_transform(x[column])
        feature_encoders[column] = label_encoder

    target_encoder = LabelEncoder()
    y = target_encoder.fit_transform(working_df[target_column].astype(str).str.strip())

    return x, pd.Series(y, name=target_column), feature_encoders, target_encoder


def stratify_if_possible(y: pd.Series) -> Optional[pd.Series]:
    """Return y for stratified splitting only when class counts make it valid."""
    class_counts = y.value_counts()
    test_rows = math.ceil(len(y) * 0.2)
    if len(class_counts) < 2:
        return None
    if class_counts.min() < 2:
        return None
    if test_rows < len(class_counts):
        return None
    return y


def evaluate_model(
    model_name: str,
    model,
    x_test: pd.DataFrame,
    y_test: pd.Series,
    target_names: List[str],
) -> str:
    """Build a readable evaluation report section for one model."""
    predictions = model.predict(x_test)
    labels = list(range(len(target_names)))

    lines = [
        f"Model: {model_name}",
        f"Accuracy : {accuracy_score(y_test, predictions):.4f}",
        f"Precision: {precision_score(y_test, predictions, average='weighted', zero_division=0):.4f}",
        f"Recall   : {recall_score(y_test, predictions, average='weighted', zero_division=0):.4f}",
        f"F1-score : {f1_score(y_test, predictions, average='weighted', zero_division=0):.4f}",
        "",
        "Classification report:",
        classification_report(
            y_test,
            predictions,
            labels=labels,
            target_names=target_names,
            zero_division=0,
        ),
        "Confusion matrix:",
        str(confusion_matrix(y_test, predictions, labels=labels)),
        "",
    ]
    return "\n".join(lines)


def encoder_mapping_lines(feature_encoders: Dict[str, LabelEncoder], target_encoder: LabelEncoder) -> List[str]:
    """Create text that explains encoded categorical values."""
    lines = ["Encoded label mapping:", ""]
    lines.append("Target label:")
    for index, label in enumerate(target_encoder.classes_):
        lines.append(f"- {index}: {label}")

    if feature_encoders:
        lines.extend(["", "Categorical feature labels:"])
        for column, encoder in feature_encoders.items():
            lines.append(f"{column}:")
            for index, label in enumerate(encoder.classes_):
                lines.append(f"- {index}: {label}")
    else:
        lines.extend(["", "Categorical feature labels: none"])
    return lines


def train_pipeline(
    data_dir: Path,
    artifacts_dir: Path,
    data_file: Optional[Path] = None,
    target_column_arg: Optional[str] = None,
) -> None:
    """Run the complete model training and export pipeline."""
    artifacts_dir.mkdir(parents=True, exist_ok=True)

    dataset_file = find_dataset_file(data_dir, data_file)
    df = load_dataset(dataset_file)
    df.columns = [clean_column_name(column) for column in df.columns]

    print(f"Dataset loaded: {dataset_file}")
    print("Columns found:")
    for column in df.columns:
        print(f"- {column}")

    target_column = detect_target_column(df.columns, target_column_arg)
    feature_columns, rename_map = detect_feature_columns(df.columns, target_column)
    if rename_map:
        df = df.rename(columns=rename_map)

    print(f"Detected target column: {target_column}")
    print("Detected feature columns:")
    for column in feature_columns:
        print(f"- {column}")

    x, y, feature_encoders, target_encoder = prepare_features(df, feature_columns, target_column)

    if len(y.value_counts()) < 2:
        raise ValueError("Target must contain at least two classes to train a classifier.")

    x_train, x_test, y_train, y_test = train_test_split(
        x,
        y,
        test_size=0.2,
        random_state=42,
        stratify=stratify_if_possible(y),
    )

    decision_tree = DecisionTreeClassifier(max_depth=4, min_samples_leaf=20, random_state=42)
    random_forest = RandomForestClassifier(
        n_estimators=200,
        random_state=42,
        min_samples_leaf=5,
        class_weight="balanced",
    )

    decision_tree.fit(x_train, y_train)
    random_forest.fit(x_train, y_train)

    target_names = [str(label) for label in target_encoder.classes_]
    report_lines = [
        "NutriScreen-ES ML Training Evaluation Report",
        "=" * 48,
        f"Dataset file: {dataset_file}",
        f"Rows used: {len(x)}",
        f"Target column: {target_column}",
        f"Feature columns: {', '.join(feature_columns)}",
        "",
        evaluate_model("DecisionTreeClassifier", decision_tree, x_test, y_test, target_names),
        evaluate_model("RandomForestClassifier", random_forest, x_test, y_test, target_names),
    ]

    tree_rules = export_text(decision_tree, feature_names=feature_columns)
    rules_lines = [
        "NutriScreen-ES Extracted Decision Tree Rules",
        "=" * 48,
        "Note: categorical features are encoded numerically. See mappings below.",
        "",
        *encoder_mapping_lines(feature_encoders, target_encoder),
        "",
        "Decision Tree rules:",
        tree_rules,
    ]

    feature_importance = pd.DataFrame(
        {
            "feature": feature_columns,
            "decision_tree_importance": decision_tree.feature_importances_,
            "random_forest_importance": random_forest.feature_importances_,
        }
    ).sort_values("random_forest_importance", ascending=False)

    joblib.dump(decision_tree, artifacts_dir / "decision_tree_model.pkl")
    joblib.dump(random_forest, artifacts_dir / "random_forest_model.pkl")
    joblib.dump(
        {
            "feature_encoders": feature_encoders,
            "target_encoder": target_encoder,
            "feature_columns": feature_columns,
            "target_column": target_column,
        },
        artifacts_dir / "encoders.pkl",
    )
    (artifacts_dir / "evaluation_report.txt").write_text("\n".join(report_lines), encoding="utf-8")
    (artifacts_dir / "extracted_rules.txt").write_text("\n".join(rules_lines), encoding="utf-8")
    feature_importance.to_csv(artifacts_dir / "feature_importance.csv", index=False)

    print(f"Training complete. Artifacts saved to: {artifacts_dir}")


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="Train NutriScreen-ES ML models and extract tree rules.")
    parser.add_argument("--data-dir", type=Path, default=DEFAULT_DATA_DIR, help="Directory containing dataset files.")
    parser.add_argument("--data-file", type=Path, default=None, help="Specific .csv, .xlsx, or .xls file to train on.")
    parser.add_argument(
        "--target-column",
        type=str,
        default=None,
        help=(
            "Target column to predict. For the current dataset, use one of: "
            "height_for_age, weight_for_age, or weight_for_height."
        ),
    )
    parser.add_argument(
        "--artifacts-dir",
        type=Path,
        default=DEFAULT_ARTIFACTS_DIR,
        help="Directory for trained models and reports.",
    )
    return parser.parse_args()


if __name__ == "__main__":
    args = parse_args()
    train_pipeline(args.data_dir, args.artifacts_dir, args.data_file, args.target_column)
