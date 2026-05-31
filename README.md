# NutriScreen-ES

NutriScreen-ES adalah prototype aplikasi web sistem pakar untuk screening awal risiko stunting dan malnutrisi anak. Sistem menggunakan pendekatan rule-based reasoning untuk membaca data anak, data antropometri, riwayat ASI/MPASI, pola makan, dan faktor risiko lingkungan, lalu menghasilkan kategori risiko beserta explanation dan rekomendasi edukatif.

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
- rules awal knowledge base.

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
- Field z-score manual masih disiapkan sebagai placeholder pengembangan.
- Rules dalam database digunakan sebagai dokumentasi knowledge base dan explanation; logic utama masih berada di `NutritionExpertSystemService`.
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
  - membangun database, form input, rules, service inferensi, dashboard, hasil screening, dan role user.
- Transfer to Production
  - menyiapkan knowledge base, dokumentasi, role admin, dan disclaimer.
- Operation and Evaluation
  - mengevaluasi hasil rules, usability, dan rekomendasi bersama expert.

## Rencana Pengembangan

- Implementasi perhitungan z-score WHO otomatis.
- Validasi rule dan bobot skor bersama ahli gizi/nutrisionis.
- Audit trail perubahan knowledge base.
- Export hasil screening ke PDF.
- Grafik pemantauan pertumbuhan per anak.
- Pembatasan akses data berdasarkan wilayah/kader.
- Penambahan modul rekomendasi edukasi yang lebih personal.
- Pengujian usability dengan calon pengguna.
