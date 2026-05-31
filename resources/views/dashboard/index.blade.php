@extends('layouts.app')

@section('title', 'Dashboard | NutriScreen-ES')

@php
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
@endphp

@section('content')
    <section class="vstack gap-4">
        <div class="alert alert-warning mb-0">
            Sistem ini hanya digunakan untuk screening awal dan edukasi. Hasil sistem tidak menggantikan diagnosis atau konsultasi dengan ahli gizi, dokter, atau tenaga kesehatan.
        </div>

        <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
            <div>
                <p class="text-uppercase small fw-semibold text-success mb-2">Dashboard prototype</p>
                <h1 class="fw-bold mb-1">Ringkasan Screening NutriScreen-ES</h1>
                <p class="text-secondary mb-0">Pantau jumlah data anak, hasil screening, dan distribusi kategori risiko awal.</p>
            </div>
            <div class="d-flex align-items-start gap-2">
                <a href="{{ route('children.index') }}" class="btn btn-success">Mulai Screening</a>
                <a href="{{ route('screenings.index') }}" class="btn btn-outline-secondary">Riwayat</a>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <span class="text-secondary small">Total Anak Terdaftar</span>
                        <div class="display-6 fw-bold mt-2">{{ $totalChildren }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <span class="text-secondary small">Total Screening Dilakukan</span>
                        <div class="display-6 fw-bold mt-2">{{ $totalScreenings }}</div>
                    </div>
                </div>
            </div>
            @foreach (['low', 'medium', 'high', 'urgent'] as $category)
                <div class="col-md-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <span class="badge text-bg-{{ $riskBadge[$category] }}">{{ $riskLabels[$category] }}</span>
                            <div class="display-6 fw-bold mt-2">{{ $riskCounts[$category] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-sm-row justify-content-between gap-3 mb-3">
                    <div>
                        <h2 class="h5 fw-semibold mb-1">Screening Terbaru</h2>
                        <p class="text-secondary mb-0">Lima data screening terakhir yang masuk ke sistem.</p>
                    </div>
                    <a href="{{ route('screenings.index') }}" class="btn btn-sm btn-outline-secondary align-self-sm-start">Lihat Semua</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama Anak</th>
                                <th>Usia</th>
                                <th>Kategori Risiko</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($latestScreenings as $screening)
                                <tr>
                                    <td>{{ $screening->screening_date->format('d M Y') }}</td>
                                    <td>{{ $screening->child->name }}</td>
                                    <td>{{ $screening->age_months }} bulan</td>
                                    <td>
                                        @if ($screening->result)
                                            <span class="badge text-bg-{{ $riskBadge[$screening->result->risk_category] ?? 'secondary' }}">
                                                {{ $riskLabels[$screening->result->risk_category] ?? strtoupper($screening->result->risk_category) }}
                                            </span>
                                        @else
                                            <span class="badge text-bg-light border text-secondary">Belum ada hasil</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('screenings.show', $screening) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-secondary">Belum ada screening.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
