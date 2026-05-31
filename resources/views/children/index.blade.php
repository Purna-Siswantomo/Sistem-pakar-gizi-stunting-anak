@extends('layouts.app')

@section('title', 'Data Anak | NutriScreen-ES')

@section('content')
    <section>
        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
            <div>
                <h1 class="fw-bold">Data Anak</h1>
                <p class="text-secondary mb-0">Pilih anak untuk melihat detail atau membuat screening baru.</p>
            </div>
            <a href="{{ route('children.create') }}" class="btn btn-success">Tambah Anak</a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>Tanggal Lahir</th>
                            <th>Orang Tua/Wali</th>
                            <th>Total Screening</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($children as $child)
                            <tr>
                                <td class="fw-semibold">{{ $child->name }}</td>
                                <td>{{ $child->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</td>
                                <td>{{ $child->birth_date->format('d M Y') }}</td>
                                <td>{{ $child->parent_name ?? '-' }}</td>
                                <td>{{ $child->screenings_count }}</td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('children.show', $child) }}" class="btn btn-outline-secondary">Detail</a>
                                        <a href="{{ route('children.screenings.create', $child) }}" class="btn btn-outline-success">Screening</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-5 text-center text-secondary">Belum ada data anak.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $children->links() }}
        </div>
    </section>
@endsection
