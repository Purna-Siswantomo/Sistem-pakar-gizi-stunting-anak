# Flowchart Alur Sistem NutriScreen-ES

Dokumen ini berisi flowchart alur utama aplikasi NutriScreen-ES untuk kebutuhan presentasi dan dokumentasi ESDLC.

Catatan update: sistem saat ini sudah mengadaptasi rule hasil training Decision Tree dari `ml_pipeline/artifacts/extracted_rules.txt`. Rule utama yang dipakai adalah `height_for_age_z_score <= -2.00` untuk menandai risiko stunting (`R5`). Sistem juga mempertahankan rule stunting berat (`R6`) pada `height_for_age_z_score <= -3.00`.

## Flowchart Utama

```mermaid
flowchart TD
    A([Mulai]) --> B{Pengguna sudah login?}
    B -- Tidak --> C[Login atau Register]
    C --> D{Autentikasi berhasil?}
    D -- Tidak --> C
    D -- Ya --> E[Dashboard]
    B -- Ya --> E

    E --> F[Kelola Data Anak]
    F --> G{Data anak sudah ada?}
    G -- Tidak --> H[Input Data Anak]
    H --> I[Simpan Data Anak]
    G -- Ya --> J[Pilih Detail Anak]
    I --> J

    J --> K[Buat Screening Baru]
    K --> L[Input Data Screening]
    L --> L1[Data Antropometri]
    L --> L2[Riwayat ASI dan MPASI]
    L --> L3[Pola Makan]
    L --> L4[Faktor Risiko Kesehatan dan Lingkungan]

    L1 --> M[Validasi Input]
    L2 --> M
    L3 --> M
    L4 --> M

    M --> N{Input valid?}
    N -- Tidak --> O[Tampilkan Error Validasi]
    O --> L
    N -- Ya --> P[Simpan Screening]

    P --> Q[Jalankan NutritionExpertSystemService]
    Q --> Q1[Evaluasi Rule Antropometri Hasil Training]
    Q1 --> R[Evaluasi Rules Lainnya]
    R --> R1{Ada Kondisi Urgent?}
    R1 -- Ya --> S[Set Kategori: Rujukan Segera]
    R1 -- Tidak --> T[Hitung Total Score Risiko]
    T --> U{Total Score}
    U -- 0 sampai 1 --> V[Set Kategori: Risiko Rendah]
    U -- 2 sampai 3 --> W[Set Kategori: Risiko Sedang]
    U -- >= 4 --> X[Set Kategori: Risiko Tinggi]

    S --> Y[Simpan Screening Result]
    V --> Y
    W --> Y
    X --> Y

    Y --> Z[Tampilkan Halaman Hasil Screening]
    Z --> Z1[Identitas Anak]
    Z --> Z2[Data Antropometri]
    Z --> Z3[Kategori Risiko dan Total Score]
    Z --> Z4[Rules Aktif]
    Z --> Z5[Explanation]
    Z --> Z6[Rekomendasi Edukatif]
    Z --> Z7[Disclaimer]

    Z --> AA{Butuh Export?}
    AA -- Ya --> AB[Export Hasil ke PDF]
    AA -- Tidak --> AC[Riwayat Screening]
    AB --> AC
    AC --> AD([Selesai])
```

## Flowchart Role dan Hak Akses

```mermaid
flowchart TD
    A([Pengguna Login]) --> B{Role Pengguna}

    B -- user --> C[Akses Dashboard]
    C --> D[Data Anak]
    D --> E[Input Screening]
    E --> F[Lihat Hasil Screening]
    F --> G[Export PDF]

    B -- admin --> H[Akses Semua Fitur User]
    H --> I[Knowledge Base Rules]
    I --> J[Tambah Rule]
    I --> K[Edit Rule]
    I --> L[Nonaktifkan Rule]
    H --> M[Lihat Semua Riwayat Screening]

    B -- selain admin/user --> N[Akses Ditolak]
```

## Flowchart Rule-Based Reasoning

```mermaid
flowchart TD
    A([Mulai Evaluasi Screening]) --> B[Cek Red Flag Urgent]
    B --> C{MUAC < 11.5 cm?}
    C -- Ya --> D[Trigger R8 dan Kategori Urgent]
    C -- Tidak --> E{WHZ/WLZ Manual < -3 SD?}
    E -- Ya --> D
    E -- Tidak --> F{Notes berisi tanda bahaya?}
    F -- Ya --> D
    F -- Tidak --> G{HAZ Manual <= -3.00 SD?}
    G -- Ya --> H[Trigger R6 dan Kategori Urgent]
    G -- Tidak --> I{HAZ Manual <= -2.00 SD?}
    I -- Ya --> J[Trigger R5 dan Tambah Score 4]
    I -- Tidak --> K[Cek Faktor Risiko Skor]
    J --> K

    K --> L[Berat Lahir Rendah: R19]
    L --> M[Prematur: R20]
    M --> N[Diare atau Infeksi Berulang: R21]
    N --> O[Food Insecurity: R23]
    O --> P[Air Minum Tidak Aman: R24]
    P --> Q[Sanitasi Tidak Layak: R25]
    Q --> R[Cek ASI dan MPASI]

    R --> S[Tidak ASI Eksklusif: R11]
    S --> T[MPASI Terlambat]
    T --> U[Frekuensi Makan Kurang: R14 atau R15]
    U --> V[Keragaman Pangan Rendah]
    V --> W[Protein Hewani Kurang: R17]

    W --> X{Total Score}
    D --> OUT_URGENT[Simpan Hasil: Urgent]
    H --> OUT_URGENT
    X -- 0 sampai 1 --> Y[Simpan Hasil: Low]
    X -- 2 sampai 3 --> Z[Simpan Hasil: Medium]
    X -- >= 4 --> AA[Simpan Hasil: High]

    OUT_URGENT --> AB([Selesai])
    Y --> AB
    Z --> AB
    AA --> AB
```

## Flowchart Pipeline ML dan Adaptasi Rule

```mermaid
flowchart TD
    A([Mulai Pipeline ML]) --> B[Simpan Dataset di ml_pipeline/data]
    B --> C[Load CSV atau Excel]
    C --> D[Normalisasi Nama Kolom]
    D --> E[Deteksi Fitur dan Target]
    E --> F[Handle Missing Value dan Encoding]
    F --> G[Split Train-Test 80:20]
    G --> H[Train Decision Tree dan Random Forest]
    H --> I[Evaluasi Model]
    I --> J[Export Rule Decision Tree]
    J --> K[Simpan Artifact di ml_pipeline/artifacts]
    K --> L{Rule Interpretable?}
    L -- Ya --> M[Adaptasi ke NutritionExpertSystemService]
    L -- Tidak --> N[Review Dataset dan Training]
    M --> O[Update RuleSeeder dan Dokumentasi]
    O --> P([Selesai])
    N --> B
```

## Ringkasan Alur untuk Presentasi

1. Pengguna login ke sistem.
2. Pengguna menambahkan atau memilih data anak.
3. Pengguna mengisi form screening.
4. Sistem memvalidasi dan menyimpan input.
5. Service sistem pakar menjalankan rules, termasuk rule stunting hasil training Decision Tree.
6. Sistem menyimpan hasil kategori risiko, skor, rules aktif, explanation, dan rekomendasi.
7. Pengguna melihat hasil screening dan dapat export PDF.
8. Admin dapat mengelola dokumentasi knowledge base rules.
9. Pipeline ML dapat dijalankan ulang saat dataset diperbarui untuk mengevaluasi model dan mengekstrak rule baru.
