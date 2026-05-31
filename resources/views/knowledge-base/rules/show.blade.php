@extends('layouts.app')

@section('title', $rule->code.' | Knowledge Base')

@section('content')
    <section class="vstack gap-4">
        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
            <div>
                <h1 class="fw-bold mb-1">{{ $rule->code }} - {{ $rule->name }}</h1>
                <p class="text-secondary mb-0">Detail dokumentasi rule knowledge base.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('knowledge-base.rules.edit', $rule) }}" class="btn btn-success">Edit Rule</a>
                <a href="{{ route('knowledge-base.rules') }}" class="btn btn-outline-secondary">Daftar Rules</a>
            </div>
        </div>

        <div class="alert alert-warning mb-0">
            Perubahan knowledge base pada sistem production hanya boleh dilakukan oleh admin setelah validasi ahli gizi atau tenaga kesehatan yang berwenang.
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
                    <span class="badge text-bg-secondary">{{ ucfirst($rule->severity) }}</span>
                    <span class="badge {{ $rule->is_active ? 'text-bg-success' : 'text-bg-light border text-secondary' }}">
                        {{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>

                <dl class="row mb-0">
                    <dt class="col-md-3 text-secondary">Description</dt>
                    <dd class="col-md-9">{{ $rule->description ?: '-' }}</dd>

                    <dt class="col-md-3 text-secondary">Condition Summary</dt>
                    <dd class="col-md-9">{{ $rule->condition_summary ?: '-' }}</dd>

                    <dt class="col-md-3 text-secondary">Explanation</dt>
                    <dd class="col-md-9">{{ $rule->explanation }}</dd>

                    <dt class="col-md-3 text-secondary">Recommendation</dt>
                    <dd class="col-md-9">{{ $rule->recommendation }}</dd>

                    <dt class="col-md-3 text-secondary">Source Reference</dt>
                    <dd class="col-md-9 mb-0">{{ $rule->source_reference }}</dd>
                </dl>
            </div>
        </div>

        @if ($rule->is_active)
            <form method="POST" action="{{ route('knowledge-base.rules.deactivate', $rule) }}" onsubmit="return confirm('Nonaktifkan rule ini?')">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-outline-danger">Nonaktifkan Rule</button>
            </form>
        @endif
    </section>
@endsection
