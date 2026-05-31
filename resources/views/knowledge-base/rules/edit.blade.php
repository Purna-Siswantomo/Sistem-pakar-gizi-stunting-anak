@extends('layouts.app')

@section('title', 'Edit Rule | NutriScreen-ES')

@section('content')
    <section>
        <div class="mb-4">
            <h1 class="fw-bold">Edit Rule {{ $rule->code }}</h1>
            <p class="text-secondary mb-0">Perbarui dokumentasi knowledge base dan sumber rujukan rule.</p>
        </div>

        <div class="alert alert-warning">
            Perubahan knowledge base pada sistem production hanya boleh dilakukan oleh admin setelah validasi ahli gizi atau tenaga kesehatan yang berwenang.
        </div>

        <form method="POST" action="{{ route('knowledge-base.rules.update', $rule) }}">
            @include('knowledge-base.rules._form', ['method' => 'PUT'])
        </form>
    </section>
@endsection
