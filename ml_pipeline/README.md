# NutriScreen-ES ML Pipeline

Pipeline ini digunakan untuk melatih model klasifikasi awal risiko stunting atau malnutrisi anak dari dataset tabular. Output utamanya adalah model machine learning, laporan evaluasi, rule dari Decision Tree, dan feature importance yang dapat ditinjau sebelum dimasukkan ke knowledge base Laravel NutriScreen-ES.

Pipeline ini berdiri sendiri di folder `ml_pipeline/` dan belum melakukan integrasi ke Laravel.

## Struktur Folder

```text
ml_pipeline/
  data/
  artifacts/
  train_nutriscreen_model.py
  requirements.txt
  README.md
```

## Format Dataset

Letakkan dataset secara manual di:

```text
ml_pipeline/data/
```

Format file yang didukung:

- `.csv`
- `.xlsx`
- `.xls`

Jika ada lebih dari satu file dataset, script akan memprioritaskan file agregat berikut:

1. `Overall Data.xlsx`
2. `Overall Data.xls`
3. `Preprocessed Data.xlsx`
4. `Preprocessed Data.xls`

Untuk memilih file tertentu, gunakan argumen `--data-file`.

## Kolom Fitur yang Didukung

Script akan membersihkan nama kolom menjadi lowercase dan snake_case, lalu mencoba mendeteksi fitur berikut:

- `gender`
- `age_months`
- `weight_kg`
- `height_cm`
- `weight_for_age_zscore`
- `height_for_age_zscore`
- `weight_for_height_zscore`

Untuk dataset saat ini, alias berikut sudah didukung:

- `Age (Month)` menjadi `age_months`
- `Weight` menjadi `weight_kg`
- `Height` menjadi `height_cm`
- `Z-Score W/A` menjadi `weight_for_age_zscore`
- `Z-Score H/A` menjadi `height_for_age_zscore`
- `Z-Score W/H` menjadi `weight_for_height_zscore`

Beberapa alias umum lain juga didukung, misalnya `sex`, `jenis_kelamin`, `usia_bulan`, `berat_badan_kg`, dan `tinggi_badan_cm`.

## Kolom Target

Script akan mencari salah satu kolom target berikut:

- `nutritional_status`
- `stunting_status`
- `status`
- `label`
- `height_for_age`
- `weight_for_age`
- `weight_for_height`

Jika kolom target tidak ditemukan, script akan menampilkan error berisi kandidat target dan daftar kolom yang terbaca dari dataset.

Pada dataset saat ini, target default adalah `height_for_age` karena pipeline memprioritaskan screening stunting. Untuk melatih target lain, gunakan `--target-column`.

## Instalasi Dependency

Dari root project Laravel, jalankan:

```bash
cd ml_pipeline
python -m venv .venv
.venv\Scripts\activate
pip install -r requirements.txt
```

Untuk Git Bash atau Linux/macOS:

```bash
cd ml_pipeline
python -m venv .venv
source .venv/bin/activate
pip install -r requirements.txt
```

## Menjalankan Training

Dari folder `ml_pipeline/`:

```bash
python train_nutriscreen_model.py
```

Atau pilih file dataset tertentu:

```bash
python train_nutriscreen_model.py --data-file data/nama_dataset.csv
```

Contoh training untuk target stunting:

```bash
python train_nutriscreen_model.py --target-column height_for_age
```

Contoh training untuk status berat badan menurut umur:

```bash
python train_nutriscreen_model.py --target-column weight_for_age
```

Contoh training untuk status berat badan menurut tinggi badan:

```bash
python train_nutriscreen_model.py --target-column weight_for_height
```

Catatan: pada file Excel dataset saat ini, beberapa nilai desimal seperti `13.5` dapat terbaca sebagai tanggal Excel. Script sudah mengubah nilai seperti `13 May 2025` kembali menjadi `13.5` untuk kolom numerik.

## Output Artifact

Setelah training berhasil, output akan tersimpan di:

```text
ml_pipeline/artifacts/
```

File yang dibuat:

- `decision_tree_model.pkl`
- `random_forest_model.pkl`
- `encoders.pkl`
- `evaluation_report.txt`
- `extracted_rules.txt`
- `feature_importance.csv`

## Catatan Interpretasi Rule

Rule di `extracted_rules.txt` dibuat dari `DecisionTreeClassifier` dengan konfigurasi:

- `max_depth=4`
- `min_samples_leaf=20`
- `random_state=42`

Konfigurasi ini sengaja dibatasi agar rule lebih mudah dibaca dan dapat dijelaskan dalam laporan akademik. Untuk fitur kategorikal seperti `gender`, nilai akan di-encode menjadi angka. Mapping encoder juga dicantumkan di file `extracted_rules.txt`.

## Evaluasi Model

`evaluation_report.txt` berisi evaluasi untuk:

- `DecisionTreeClassifier`
- `RandomForestClassifier`

Metrik yang dicatat:

- accuracy
- precision
- recall
- f1-score
- classification report
- confusion matrix

## Batasan Tahap Ini

Pipeline ini belum:

- mengubah route, controller, model, migration, view, atau config Laravel
- membuat integrasi ke Laravel
- membuat database baru
- membuat halaman web baru

Tahap ini hanya fokus pada training machine learning dan ekstraksi rule IF-THEN dari Decision Tree.
