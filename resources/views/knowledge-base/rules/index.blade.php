@extends('layouts.app')

@section('title', 'Knowledge Base Rules | NutriScreen-ES')

@section('content')
    <section>
        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
            <div>
                <h1 class="fw-bold">Knowledge Base Rules</h1>
                <p class="text-secondary mb-0">Dokumentasi rules, explanation, rekomendasi, dan sumber pedoman yang digunakan sistem.</p>
            </div>
            <a href="{{ route('knowledge-base.rules.create') }}" class="btn btn-success">Tambah Rule</a>
        </div>

        <div class="alert alert-warning">
            Perubahan knowledge base pada sistem production hanya boleh dilakukan oleh admin setelah validasi ahli gizi atau tenaga kesehatan yang berwenang.
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Nama Rule</th>
                            <th>Severity</th>
                            <th>Status</th>
                            <th>Sumber</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rules as $rule)
                            <tr>
                                <td class="fw-semibold">{{ $rule->code }}</td>
                                <td>{{ $rule->name }}</td>
                                <td><span class="badge text-bg-secondary">{{ ucfirst($rule->severity) }}</span></td>
                                <td>
                                    <span class="badge {{ $rule->is_active ? 'text-bg-success' : 'text-bg-light border text-secondary' }}">
                                        {{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="text-secondary">{{ str($rule->source_reference)->limit(60) }}</td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('knowledge-base.rules.show', $rule) }}" class="btn btn-outline-secondary">Detail</a>
                                        <a href="{{ route('knowledge-base.rules.edit', $rule) }}" class="btn btn-outline-success">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-5 text-center text-secondary">Belum ada rules.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $rules->links() }}
        </div>
    </section>
@endsection
