@extends('layouts.app')

@section('title', 'Detail Anak | NutriScreen-ES')

@section('content')
    <section>
        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
            <div>
                <h1 class="fw-bold">{{ $child->name }}</h1>
                <p class="text-secondary mb-0">Detail anak dan riwayat screening yang sudah tersimpan.</p>
            </div>
            <a href="{{ route('children.screenings.create', $child) }}" class="btn btn-success">Screening Baru</a>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-semibold mb-3">Profil Anak</h2>
                        <dl class="mb-0">
                            <dt class="text-secondary small">Jenis Kelamin</dt>
                            <dd>{{ $child->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</dd>
                            <dt class="text-secondary small">Tanggal Lahir</dt>
                            <dd>{{ $child->birth_date->format('d M Y') }}</dd>
                            <dt class="text-secondary small">Orang Tua/Wali</dt>
                            <dd>{{ $child->parent_name ?? '-' }}</dd>
                            <dt class="text-secondary small">Alamat</dt>
                            <dd class="mb-0">{{ $child->address ?? '-' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-semibold mb-3">Riwayat Screening</h2>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Usia</th>
                                        <th>BB</th>
                                        <th>TB/PB</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($child->screenings as $screening)
                                        <tr>
                                            <td>{{ $screening->screening_date->format('d M Y') }}</td>
                                            <td>{{ $screening->age_months }} bulan</td>
                                            <td>{{ $screening->weight_kg }} kg</td>
                                            <td>{{ $screening->height_cm }} cm</td>
                                            <td class="text-end">
                                                <a href="{{ route('screenings.show', $screening) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-4 text-center text-secondary">Belum ada screening untuk anak ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
