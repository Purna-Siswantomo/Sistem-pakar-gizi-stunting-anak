@extends('layouts.app')

@section('title', 'Tentang Sistem | NutriScreen-ES')

@section('content')
    <section class="vstack gap-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <p class="text-uppercase small fw-semibold text-success mb-2">Tentang Sistem</p>
                <h1 class="fw-bold mb-3">NutriScreen-ES</h1>
                <p class="lead text-secondary mb-0">
                    NutriScreen-ES adalah prototype sistem pakar berbasis rule-based reasoning untuk screening awal risiko stunting dan malnutrisi anak.
                </p>
            </div>
        </div>

        <div class="alert alert-warning mb-0">
            Sistem ini hanya digunakan untuk screening awal dan edukasi. Hasil sistem tidak menggantikan diagnosis atau konsultasi dengan ahli gizi, dokter, atau tenaga kesehatan.
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-semibold mb-3">Profil Sistem</h2>
                        <dl class="row mb-0">
                            <dt class="col-sm-4 text-secondary">Nama Sistem</dt>
                            <dd class="col-sm-8">NutriScreen-ES</dd>

                            <dt class="col-sm-4 text-secondary">Tujuan</dt>
                            <dd class="col-sm-8">Screening awal risiko stunting dan malnutrisi anak.</dd>

                            <dt class="col-sm-4 text-secondary">Expert</dt>
                            <dd class="col-sm-8">Ahli gizi atau nutrisionis.</dd>

                            <dt class="col-sm-4 text-secondary">User</dt>
                            <dd class="col-sm-8">Orang tua, kader posyandu, dan tenaga kesehatan dasar.</dd>

                            <dt class="col-sm-4 text-secondary">Batasan</dt>
                            <dd class="col-sm-8 mb-0">Bukan alat diagnosis medis dan tidak menggantikan konsultasi profesional.</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-semibold mb-3">Cara Kerja Singkat</h2>
                        <ol class="mb-0 text-secondary">
                            <li>Pengguna menginput data anak, antropometri, riwayat makan, dan faktor risiko lingkungan.</li>
                            <li>Sistem menjalankan rules pada service rule-based reasoning.</li>
                            <li>Sistem menghasilkan kategori risiko: rendah, sedang, tinggi, atau rujukan segera.</li>
                            <li>Sistem menampilkan explanation rules yang aktif dan rekomendasi edukatif.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-semibold mb-3">Dasar Knowledge</h2>
                        <ul class="mb-0 text-secondary">
                            <li>Standar antropometri WHO untuk indikator pertumbuhan anak.</li>
                            <li>Permenkes RI tentang Standar Antropometri Anak.</li>
                            <li>Penelitian terkait faktor risiko stunting dan malnutrisi, seperti berat lahir rendah, prematur, infeksi berulang, pola makan, sanitasi, dan ketahanan pangan.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-semibold mb-3">ESDLC</h2>
                        <div class="vstack gap-3">
                            <div>
                                <h3 class="h6 fw-semibold mb-1">Problem Identification</h3>
                                <p class="text-secondary mb-0">Mengidentifikasi kebutuhan screening awal risiko stunting dan malnutrisi yang mudah dipahami pengguna.</p>
                            </div>
                            <div>
                                <h3 class="h6 fw-semibold mb-1">System Development</h3>
                                <p class="text-secondary mb-0">Membangun prototype Laravel dengan database, rules, service inferensi, dan halaman hasil.</p>
                            </div>
                            <div>
                                <h3 class="h6 fw-semibold mb-1">Transfer to Production</h3>
                                <p class="text-secondary mb-0">Menyiapkan dokumentasi, role admin, knowledge base, dan validasi sebelum sistem digunakan lebih luas.</p>
                            </div>
                            <div>
                                <h3 class="h6 fw-semibold mb-1">Operation and Evaluation</h3>
                                <p class="text-secondary mb-0">Mengevaluasi akurasi rules, usability, dan rekomendasi bersama ahli gizi atau tenaga kesehatan.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
