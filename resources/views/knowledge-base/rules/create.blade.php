@extends('layouts.app')

@section('title', 'Tambah Rule | NutriScreen-ES')

@section('content')
    <section>
        <div class="mb-4">
            <h1 class="fw-bold">Tambah Rule</h1>
            <p class="text-secondary mb-0">Rules di database digunakan sebagai dokumentasi knowledge base dan explanation.</p>
        </div>

        <div class="alert alert-warning">
            Perubahan knowledge base pada sistem production hanya boleh dilakukan oleh admin setelah validasi ahli gizi atau tenaga kesehatan yang berwenang.
        </div>

        <form method="POST" action="{{ route('knowledge-base.rules.store') }}">
            @include('knowledge-base.rules._form')
        </form>
    </section>
@endsection
