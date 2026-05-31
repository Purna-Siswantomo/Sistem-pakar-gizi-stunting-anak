@extends('layouts.app')

@section('title', 'Tambah Anak | NutriScreen-ES')

@section('content')
    <section class="row justify-content-center">
        <div class="col-lg-8">
            <div class="mb-4">
                <h1 class="fw-bold">Tambah Data Anak</h1>
                <p class="text-secondary mb-0">Data ini dipakai sebagai profil dasar sebelum screening dilakukan.</p>
            </div>

            <form method="POST" action="{{ route('children.store') }}" class="card border-0 shadow-sm">
                @csrf
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nama Anak <span class="text-danger">*</span></label>
                            <input id="name" name="name" type="text" value="{{ old('name') }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="gender" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select id="gender" name="gender" class="form-select" required>
                                <option value="">Pilih</option>
                                <option value="male" @selected(old('gender') === 'male')>Laki-laki</option>
                                <option value="female" @selected(old('gender') === 'female')>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="birth_date" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input id="birth_date" name="birth_date" type="date" value="{{ old('birth_date') }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="parent_name" class="form-label">Nama Orang Tua/Wali</label>
                            <input id="parent_name" name="parent_name" type="text" value="{{ old('parent_name') }}" class="form-control">
                        </div>
                        <div class="col-12">
                            <label for="address" class="form-label">Alamat</label>
                            <textarea id="address" name="address" rows="3" class="form-control">{{ old('address') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex justify-content-between">
                    <a href="{{ route('children.index') }}" class="btn btn-outline-secondary">Kembali</a>
                    <button type="submit" class="btn btn-success">Simpan Anak</button>
                </div>
            </form>
        </div>
    </section>
@endsection
