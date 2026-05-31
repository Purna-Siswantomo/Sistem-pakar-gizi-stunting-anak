@extends('layouts.app')

@section('title', 'Riwayat Screening | NutriScreen-ES')

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
    <section>
        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
            <div>
                <h1 class="fw-bold">Riwayat Screening</h1>
                <p class="text-secondary mb-0">Daftar screening yang sudah tersimpan beserta kategori risiko awal.</p>
            </div>
            <a href="{{ route('children.index') }}" class="btn btn-success">Screening Baru</a>
        </div>

        <form method="GET" action="{{ route('screenings.index') }}" class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="search" class="form-label">Search Nama Anak</label>
                        <input id="search" name="search" type="search" value="{{ $filters['search'] }}" class="form-control" placeholder="Contoh: Alya">
                    </div>
                    <div class="col-md-4">
                        <label for="risk_category" class="form-label">Filter Kategori Risiko</label>
                        <select id="risk_category" name="risk_category" class="form-select">
                            <option value="">Semua kategori</option>
                            @foreach ($riskLabels as $value => $label)
                                <option value="{{ $value }}" @selected($filters['risk_category'] === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success flex-fill">Terapkan</button>
                            <a href="{{ route('screenings.index') }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal Screening</th>
                            <th>Nama Anak</th>
                            <th>Usia Bulan</th>
                            <th>Berat Badan</th>
                            <th>Tinggi Badan</th>
                            <th>Kategori Risiko</th>
                            <th class="text-end">Detail Hasil</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($screenings as $screening)
                            <tr>
                                <td>{{ $screening->screening_date->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('children.show', $screening->child) }}" class="text-success fw-semibold text-decoration-none">
                                        {{ $screening->child->name }}
                                    </a>
                                </td>
                                <td>{{ $screening->age_months }} bulan</td>
                                <td>{{ $screening->weight_kg }} kg</td>
                                <td>{{ $screening->height_cm }} cm</td>
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
                                <td colspan="7" class="py-5 text-center text-secondary">Tidak ada data screening sesuai filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $screenings->links() }}
        </div>
    </section>
@endsection
