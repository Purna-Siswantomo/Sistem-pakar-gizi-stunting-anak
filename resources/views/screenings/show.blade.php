@extends('layouts.app')

@section('title', 'Hasil Screening | NutriScreen-ES')

@php
    $boolLabel = fn ($value) => is_null($value) ? 'Belum diketahui' : ($value ? 'Ya' : 'Tidak');
    $riskLabels = [
        'low' => 'Risiko Rendah',
        'medium' => 'Risiko Sedang',
        'high' => 'Risiko Tinggi',
        'urgent' => 'Rujukan Segera',
    ];
    $riskBadge = [
        'low' => 'success',
        'medium' => 'warning',
        'high' => 'danger',
        'urgent' => 'dark',
    ];
    $severityBadge = [
        'info' => 'secondary',
        'low' => 'success',
        'medium' => 'warning',
        'high' => 'danger',
        'urgent' => 'dark',
    ];
    $recommendations = $screening->result
        ? array_values(array_filter(preg_split('/\r\n|\r|\n/', $screening->result->recommendations)))
        : [];
@endphp

@section('content')
    <section class="vstack gap-4">
        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
            <div>
                <h1 class="fw-bold mb-1">Hasil Screening</h1>
                <p class="text-secondary mb-0">Ringkasan hasil screening awal NutriScreen-ES.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('screenings.pdf', $screening) }}" class="btn btn-success">Export PDF</a>
                <a href="{{ route('screenings.index') }}" class="btn btn-outline-secondary">Riwayat</a>
                <a href="{{ route('children.show', $screening->child) }}" class="btn btn-outline-success">Detail Anak</a>
            </div>
        </div>

        <div class="alert alert-warning mb-0">
            Hasil ini merupakan screening awal dan tidak menggantikan diagnosis dari ahli gizi, dokter, atau tenaga kesehatan. Jika anak masuk kategori risiko sedang, tinggi, atau rujukan segera, pengguna disarankan berkonsultasi ke fasilitas kesehatan.
        </div>

        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-semibold mb-3">Identitas Anak</h2>
                        <dl class="row mb-0">
                            <dt class="col-sm-5 text-secondary">Nama</dt>
                            <dd class="col-sm-7">{{ $screening->child->name }}</dd>
                            <dt class="col-sm-5 text-secondary">Jenis Kelamin</dt>
                            <dd class="col-sm-7">{{ $screening->child->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</dd>
                            <dt class="col-sm-5 text-secondary">Usia Screening</dt>
                            <dd class="col-sm-7">{{ $screening->age_months }} bulan</dd>
                            <dt class="col-sm-5 text-secondary">Tanggal Screening</dt>
                            <dd class="col-sm-7 mb-0">{{ $screening->screening_date->format('d M Y') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-semibold mb-3">Data Antropometri</h2>
                        <div class="row g-3">
                            <div class="col-sm-6 col-lg-3">
                                <span class="text-secondary small d-block">Berat Badan</span>
                                <span class="fw-semibold">{{ $screening->weight_kg }} kg</span>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <span class="text-secondary small d-block">Tinggi/Panjang</span>
                                <span class="fw-semibold">{{ $screening->height_cm }} cm</span>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <span class="text-secondary small d-block">MUAC</span>
                                <span class="fw-semibold">{{ $screening->muac_cm ? $screening->muac_cm.' cm' : '-' }}</span>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <span class="text-secondary small d-block">Berat Lahir</span>
                                <span class="fw-semibold">{{ $screening->birth_weight_gram ? $screening->birth_weight_gram.' gram' : '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($screening->result)
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                        <div>
                            <h2 class="h5 fw-semibold mb-3">Hasil Sistem</h2>
                            <div class="d-flex flex-wrap align-items-center gap-3">
                                <span class="badge rounded-pill text-bg-{{ $riskBadge[$screening->result->risk_category] ?? 'secondary' }} px-3 py-2">
                                    {{ $riskLabels[$screening->result->risk_category] ?? strtoupper($screening->result->risk_category) }}
                                </span>
                                <span class="text-secondary">Total score: <strong class="text-dark">{{ $screening->result->total_score }}</strong></span>
                            </div>
                            <p class="text-secondary mt-3 mb-0">{{ $screening->result->summary }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 fw-semibold mb-3">Rules yang Aktif</h2>
                    @if ($triggeredRuleDetails->isNotEmpty())
                        <div class="vstack gap-3">
                            @foreach ($triggeredRuleDetails as $rule)
                                <article class="border rounded p-3 bg-white">
                                    <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-2">
                                        <div>
                                            <span class="fw-semibold">{{ $rule['code'] }}</span>
                                            <span class="text-secondary">- {{ $rule['name'] }}</span>
                                        </div>
                                        <span class="badge align-self-start text-bg-{{ $severityBadge[$rule['severity']] ?? 'secondary' }}">
                                            {{ ucfirst($rule['severity']) }}
                                        </span>
                                    </div>
                                    <p class="mb-2 text-secondary">{{ $rule['explanation'] }}</p>
                                    <p class="mb-0"><span class="fw-semibold">Rekomendasi:</span> {{ $rule['recommendation'] }}</p>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <p class="text-secondary mb-0">Tidak ada rule risiko yang aktif dari data screening ini.</p>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 fw-semibold mb-3">Rekomendasi Edukatif</h2>
                    <ul class="mb-0 text-secondary">
                        @foreach ($recommendations as $recommendation)
                            <li>{{ $recommendation }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h2 class="h5 fw-semibold mb-3">Data Pendukung</h2>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <dl class="row mb-0">
                            <dt class="col-sm-6 text-secondary">Prematur</dt>
                            <dd class="col-sm-6">{{ $boolLabel($screening->is_premature) }}</dd>
                            <dt class="col-sm-6 text-secondary">ASI Eksklusif</dt>
                            <dd class="col-sm-6">{{ $boolLabel($screening->exclusive_breastfeeding) }}</dd>
                            <dt class="col-sm-6 text-secondary">Sudah MPASI</dt>
                            <dd class="col-sm-6">{{ $boolLabel($screening->complementary_feeding_started) }}</dd>
                            <dt class="col-sm-6 text-secondary">Frekuensi Makan</dt>
                            <dd class="col-sm-6">{{ $screening->meal_frequency_per_day ? $screening->meal_frequency_per_day.' kali/hari' : '-' }}</dd>
                            <dt class="col-sm-6 text-secondary">Protein Hewani</dt>
                            <dd class="col-sm-6 mb-0">{{ $screening->animal_protein_frequency ?? '-' }}</dd>
                        </dl>
                    </div>
                    <div class="col-lg-6">
                        <dl class="row mb-0">
                            <dt class="col-sm-6 text-secondary">Diare Berulang</dt>
                            <dd class="col-sm-6">{{ $boolLabel($screening->has_recurrent_diarrhea) }}</dd>
                            <dt class="col-sm-6 text-secondary">Infeksi Berulang</dt>
                            <dd class="col-sm-6">{{ $boolLabel($screening->has_recurrent_infection) }}</dd>
                            <dt class="col-sm-6 text-secondary">Ketahanan Pangan Rendah</dt>
                            <dd class="col-sm-6">{{ $boolLabel($screening->food_insecurity) }}</dd>
                            <dt class="col-sm-6 text-secondary">Air Minum Aman</dt>
                            <dd class="col-sm-6">{{ $boolLabel($screening->safe_drinking_water) }}</dd>
                            <dt class="col-sm-6 text-secondary">Sanitasi Layak</dt>
                            <dd class="col-sm-6 mb-0">{{ $boolLabel($screening->proper_sanitation) }}</dd>
                        </dl>
                    </div>
                    <div class="col-12">
                        <span class="text-secondary small d-block">Catatan Tambahan</span>
                        <p class="mb-0">{{ $screening->notes ?: 'Tidak ada catatan tambahan.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
