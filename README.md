# NutriScreen-ES

NutriScreen-ES adalah prototype aplikasi web sistem pakar untuk screening awal risiko stunting dan malnutrisi anak. Sistem menggunakan pendekatan rule-based reasoning untuk membaca data anak, data antropometri, riwayat ASI/MPASI, pola makan, dan faktor risiko lingkungan, lalu menghasilkan kategori risiko beserta explanation dan rekomendasi edukatif.

Project ini juga memiliki pipeline machine learning terpisah di folder `ml_pipeline/`. Pipeline tersebut digunakan untuk melatih model klasifikasi tabular dan mengekstrak rule Decision Tree yang kemudian dapat diadaptasi ke knowledge base Laravel.

Sistem ini dibuat untuk kebutuhan demonstrasi tugas ESDLC. NutriScreen-ES bukan alat diagnosis medis.

## Fitur Utama

- Authentication dengan role sederhana: `admin` dan `user`.
- Dashboard ringkas:
  - total anak terdaftar,
  - total screening,
  - jumlah risiko rendah, sedang, tinggi, dan rujukan segera,
  - screening terbaru.
- Manajemen data anak.
- Input screening anak.
- Rule-based reasoning melalui `App\Services\NutritionExpertSystemService`.
- Rule stunting hasil training Decision Tree:
  - `height_for_age_z_score <= -2.00` mengaktifkan `R5` Risiko stunting.
  - `height_for_age_z_score <= -3.00` mengaktifkan `R6` Risiko stunting berat.
- Halaman hasil screening dengan:
  - identitas anak,
  - data antropometri,
  - kategori risiko,
  - total score,
  - rules aktif,
  - explanation,
  - rekomendasi edukatif.
- Riwayat screening dengan filter kategori risiko dan pencarian nama anak.
- Knowledge base rules untuk dokumentasi rules, explanation, rekomendasi, severity, status aktif, dan sumber referensi.
- Middleware admin untuk membatasi akses knowledge base.
- Pipeline ML lokal untuk training model, evaluasi, ekstraksi rule, dan feature importance.

## Flowchart Sistem

Flowchart alur sistem tersedia di:

```text
docs/flowchart.md
```

File tersebut berisi flowchart utama, flowchart role dan hak akses, serta flowchart rule-based reasoning dalam format Mermaid.

## Pipeline Machine Learning

Pipeline ML berada di:

```text
ml_pipeline/
```

Struktur utama:

- `ml_pipeline/data/`
  - tempat menyimpan dataset `.csv`, `.xlsx`, atau `.xls`.
- `ml_pipeline/artifacts/`
  - tempat output model, evaluasi, extracted rules, dan feature importance.
- `ml_pipeline/train_nutriscreen_model.py`
  - script training model.
- `ml_pipeline/requirements.txt`
  - dependency Python.
- `ml_pipeline/README.md`
  - dokumentasi teknis cara menjalankan training.

Dataset yang digunakan pada tahap ini berada di `ml_pipeline/data/`, terutama `Overall Data.xlsx`. Script training membaca kolom dataset seperti:

- `Gender`
- `Age (Month)`
- `Weight`
- `Height`
- `Z-Score W/A`
- `Z-Score H/A`
- `Z-Score W/H`
- `Height for Age`
- `Weight for Age`
- `Weight for Height`

Training default memakai target `height_for_age` untuk screening stunting. Model yang dilatih:

- `DecisionTreeClassifier`
- `RandomForestClassifier`

Output training:

- `decision_tree_model.pkl`
- `random_forest_model.pkl`
- `encoders.pkl`
- `evaluation_report.txt`
- `extracted_rules.txt`
- `feature_importance.csv`

Rule utama yang dihasilkan dari Decision Tree:

```text
IF height_for_age_zscore <= -2.00 THEN Stunted
IF height_for_age_zscore > -2.00 THEN Not Stunted
```

Rule tersebut sudah diadaptasi ke Laravel melalui `NutritionExpertSystemService` sebagai rule `R5`. Untuk kebutuhan klinis dan knowledge base, sistem tetap membedakan kondisi stunting berat melalui `R6` pada threshold `height_for_age_z_score <= -3.00`.

Menjalankan training:

```bash
cd ml_pipeline
python -m venv .venv
.venv\Scripts\activate
pip install -r requirements.txt
python train_nutriscreen_model.py
```

Training target lain:

```bash
python train_nutriscreen_model.py --target-column weight_for_age
python train_nutriscreen_model.py --target-column weight_for_height
```

## Struktur Database

Tabel utama:

- `users`
  - menyimpan akun login dan role (`admin` atau `user`).
- `children`
  - menyimpan profil anak: nama, jenis kelamin, tanggal lahir, nama orang tua/wali, dan alamat.
- `screenings`
  - menyimpan data screening: tanggal screening, usia bulan, berat badan, tinggi/panjang badan, MUAC, berat lahir, prematur, ASI/MPASI, pola makan, infeksi, sanitasi, air minum, dan catatan.
- `rules`
  - dokumentasi knowledge base rules: code, name, description, condition summary, explanation, recommendation, severity, source reference, dan status aktif.
- `screening_results`
  - menyimpan hasil inferensi: risk category, total score, summary, recommendations, explanations, dan triggered rules.

Relasi utama:

- `Child hasMany Screening`
- `Screening belongsTo Child`
- `Screening hasOne ScreeningResult`
- `ScreeningResult belongsTo Screening`

## Cara Install

Prasyarat:

- PHP 8.3 atau lebih baru
- Composer
- MySQL

Langkah install:

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Atur database pada `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nutriscreen_es
DB_USERNAME=root
DB_PASSWORD=
```

Catatan: project memakai Bootstrap CDN untuk tampilan utama, sehingga build Vite tidak wajib untuk demo. Jika ingin memakai asset Vite dari Breeze, pastikan versi Node memenuhi kebutuhan Vite.

## Migration dan Seeder

Jalankan:

```bash
php artisan migrate:fresh --seed
```

Seeder akan membuat:

- akun admin,
- akun user,
- rules awal knowledge base, termasuk `R5` dan `R6` yang sudah disesuaikan dengan rule hasil training Decision Tree.

## Akun Login Default

Admin:

```text
Email: admin@example.com
Password: password
Role: admin
```

User:

```text
Email: user@example.com
Password: password
Role: user
```

## Menjalankan Aplikasi

```bash
php artisan serve --host=127.0.0.1 --port=8001
```

Buka:

```text
http://127.0.0.1:8001/login
```

## Akses Role

User biasa dapat:

- melihat dashboard,
- menambah data anak,
- melakukan screening,
- melihat hasil screening.

Admin dapat:

- semua akses user,
- mengelola knowledge base rules,
- melihat semua data screening,
- mengubah atau menonaktifkan rules.

## Batasan Sistem

- NutriScreen-ES hanya untuk screening awal dan edukasi.
- Hasil sistem tidak menggantikan diagnosis dari ahli gizi, dokter, atau tenaga kesehatan.
- Perhitungan z-score WHO otomatis belum diimplementasikan.
- Field z-score manual digunakan untuk menjalankan rule antropometri, termasuk rule stunting hasil training Decision Tree.
- Rules dalam database digunakan sebagai dokumentasi knowledge base dan explanation; logic utama masih berada di `NutritionExpertSystemService`.
- Pipeline ML belum dipanggil langsung oleh aplikasi Laravel saat runtime. Artifact model disimpan untuk dokumentasi, evaluasi, dan ekstraksi rule.
- Akurasi model stunting sangat tinggi karena label `Height for Age` pada dataset diturunkan langsung dari `Z-Score H/A`; karena itu rule yang diadaptasi adalah threshold interpretable, bukan prediksi model sebagai black box.
- Validasi rules dan rekomendasi harus dilakukan oleh ahli gizi atau tenaga kesehatan sebelum digunakan di lingkungan production.

## Dasar Knowledge

- Standar antropometri WHO.
- Permenkes RI tentang Standar Antropometri Anak.
- Literatur terkait faktor risiko stunting dan malnutrisi, seperti berat lahir rendah, prematur, infeksi berulang, food insecurity, sanitasi, air minum, pola makan, dan konsumsi protein hewani.

## ESDLC

Tahapan yang direpresentasikan dalam prototype:

- Problem Identification
  - mengidentifikasi kebutuhan screening awal risiko stunting dan malnutrisi.
- System Development
  - membangun database, form input, rules, service inferensi, pipeline ML, ekstraksi rule, dashboard, hasil screening, dan role user.
- Transfer to Production
  - menyiapkan knowledge base, dokumentasi, artifact training, role admin, dan disclaimer.
- Operation and Evaluation
  - mengevaluasi hasil model, rules, usability, dan rekomendasi bersama expert.

## Rencana Pengembangan

- Implementasi perhitungan z-score WHO otomatis.
- Validasi rule dan bobot skor bersama ahli gizi/nutrisionis.
- Integrasi pipeline ML yang lebih formal untuk versioning dataset, artifact, dan rule hasil training.
- Audit trail perubahan knowledge base.
- Penyempurnaan template dan isi export PDF.
- Grafik pemantauan pertumbuhan per anak.
- Pembatasan akses data berdasarkan wilayah/kader.
- Penambahan modul rekomendasi edukasi yang lebih personal.
- Pengujian usability dengan calon pengguna.
