@extends('layouts.app')

@section('title', 'Screening Baru | NutriScreen-ES')

@section('content')
    <section>
        <div class="mb-4">
            <h1 class="fw-bold">Screening Baru</h1>
            <p class="text-secondary mb-0">Input data screening awal untuk {{ $child->name }}. Logic sistem pakar belum dijalankan pada tahap ini.</p>
        </div>

        <form method="POST" action="{{ route('children.screenings.store', $child) }}" class="vstack gap-4" id="screening-form">
            @csrf

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 fw-semibold">Data Anak</h2>
                    <div class="row g-3 mt-1">
                        <div class="col-md-4">
                            <label class="form-label">Nama Anak</label>
                            <input name="name" type="text" value="{{ old('name', $child->name) }}" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="gender" class="form-select" readonly>
                                <option value="male" @selected(old('gender', $child->gender) === 'male')>Laki-laki</option>
                                <option value="female" @selected(old('gender', $child->gender) === 'female')>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Lahir</label>
                            <input id="birth_date" name="birth_date" type="date" value="{{ old('birth_date', $child->birth_date->format('Y-m-d')) }}" class="form-control" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 fw-semibold">Antropometri</h2>
                    <div class="row g-3 mt-1">
                        <div class="col-md-3">
                            <label for="screening_date" class="form-label">Tanggal Screening <span class="text-danger">*</span></label>
                            <input id="screening_date" name="screening_date" type="date" value="{{ old('screening_date', now()->format('Y-m-d')) }}" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label for="age_months" class="form-label">Usia Bulan <span class="text-danger">*</span></label>
                            <input id="age_months" name="age_months" type="number" min="0" value="{{ old('age_months') }}" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label for="weight_kg" class="form-label">Berat Badan (kg) <span class="text-danger">*</span></label>
                            <input id="weight_kg" name="weight_kg" type="number" min="0" step="0.01" value="{{ old('weight_kg') }}" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label for="height_cm" class="form-label">Tinggi/Panjang (cm) <span class="text-danger">*</span></label>
                            <input id="height_cm" name="height_cm" type="number" min="0" step="0.01" value="{{ old('height_cm') }}" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label for="muac_cm" class="form-label">MUAC (cm)</label>
                            <input id="muac_cm" name="muac_cm" type="number" min="0" step="0.1" value="{{ old('muac_cm') }}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="birth_weight_gram" class="form-label">Berat Lahir (gram)</label>
                            <input id="birth_weight_gram" name="birth_weight_gram" type="number" min="0" value="{{ old('birth_weight_gram') }}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="height_for_age_z_score" class="form-label">HAZ Manual</label>
                            <input id="height_for_age_z_score" name="height_for_age_z_score" type="number" step="0.01" value="{{ old('height_for_age_z_score') }}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="weight_for_height_z_score" class="form-label">WHZ/WLZ Manual</label>
                            <input id="weight_for_height_z_score" name="weight_for_height_z_score" type="number" step="0.01" value="{{ old('weight_for_height_z_score') }}" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 fw-semibold">Riwayat Makan dan Faktor Risiko</h2>
                    <div class="row g-3 mt-1">
                        <div class="col-md-3">
                            <label class="form-label">Lahir Prematur</label>
                            <select name="is_premature" class="form-select">
                                <option value="0" @selected(old('is_premature', '0') === '0')>Tidak</option>
                                <option value="1" @selected(old('is_premature') === '1')>Ya</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Edema</label>
                            <select name="has_edema" class="form-select">
                                <option value="0" @selected(old('has_edema', '0') === '0')>Tidak</option>
                                <option value="1" @selected(old('has_edema') === '1')>Ya</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">ASI Eksklusif</label>
                            <select name="exclusive_breastfeeding" class="form-select">
                                <option value="">Belum diketahui</option>
                                <option value="1" @selected(old('exclusive_breastfeeding') === '1')>Ya</option>
                                <option value="0" @selected(old('exclusive_breastfeeding') === '0')>Tidak</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sudah Mulai MPASI</label>
                            <select name="complementary_feeding_started" class="form-select">
                                <option value="">Belum diketahui</option>
                                <option value="1" @selected(old('complementary_feeding_started') === '1')>Ya</option>
                                <option value="0" @selected(old('complementary_feeding_started') === '0')>Tidak</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="complementary_feeding_age_month" class="form-label">Usia Mulai MPASI</label>
                            <input id="complementary_feeding_age_month" name="complementary_feeding_age_month" type="number" min="0" value="{{ old('complementary_feeding_age_month') }}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="meal_frequency_per_day" class="form-label">Frekuensi Makan/Hari</label>
                            <input id="meal_frequency_per_day" name="meal_frequency_per_day" type="number" min="0" value="{{ old('meal_frequency_per_day') }}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="dietary_diversity_score" class="form-label">Skor Keragaman Pangan</label>
                            <input id="dietary_diversity_score" name="dietary_diversity_score" type="number" min="0" value="{{ old('dietary_diversity_score') }}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Protein Hewani</label>
                            <select name="animal_protein_frequency" class="form-select">
                                <option value="">Belum diketahui</option>
                                @foreach (['never' => 'Never', 'rare' => 'Rare', 'sometimes' => 'Sometimes', 'often' => 'Often'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('animal_protein_frequency') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 fw-semibold">Kesehatan dan Lingkungan</h2>
                    <div class="row g-3 mt-1">
                        <div class="col-md-3">
                            <label class="form-label">Diare Berulang</label>
                            <select name="has_recurrent_diarrhea" class="form-select">
                                <option value="0" @selected(old('has_recurrent_diarrhea', '0') === '0')>Tidak</option>
                                <option value="1" @selected(old('has_recurrent_diarrhea') === '1')>Ya</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Infeksi Berulang</label>
                            <select name="has_recurrent_infection" class="form-select">
                                <option value="0" @selected(old('has_recurrent_infection', '0') === '0')>Tidak</option>
                                <option value="1" @selected(old('has_recurrent_infection') === '1')>Ya</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Imunisasi Lengkap</label>
                            <select name="immunization_complete" class="form-select">
                                <option value="">Belum diketahui</option>
                                <option value="1" @selected(old('immunization_complete') === '1')>Ya</option>
                                <option value="0" @selected(old('immunization_complete') === '0')>Tidak</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Ketahanan Pangan Rendah</label>
                            <select name="food_insecurity" class="form-select">
                                <option value="0" @selected(old('food_insecurity', '0') === '0')>Tidak</option>
                                <option value="1" @selected(old('food_insecurity') === '1')>Ya</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Air Minum Aman</label>
                            <select name="safe_drinking_water" class="form-select">
                                <option value="">Belum diketahui</option>
                                <option value="1" @selected(old('safe_drinking_water') === '1')>Ya</option>
                                <option value="0" @selected(old('safe_drinking_water') === '0')>Tidak</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sanitasi Layak</label>
                            <select name="proper_sanitation" class="form-select">
                                <option value="">Belum diketahui</option>
                                <option value="1" @selected(old('proper_sanitation') === '1')>Ya</option>
                                <option value="0" @selected(old('proper_sanitation') === '0')>Tidak</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="notes" class="form-label">Catatan Tambahan</label>
                            <textarea id="notes" name="notes" rows="4" class="form-control">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('children.show', $child) }}" class="btn btn-outline-secondary">Kembali</a>
                <button type="submit" class="btn btn-success">Simpan Screening</button>
            </div>
        </form>
    </section>

    <script>
        const birthDateInput = document.getElementById('birth_date');
        const screeningDateInput = document.getElementById('screening_date');
        const ageMonthsInput = document.getElementById('age_months');

        function updateAgeMonths() {
            if (!birthDateInput.value || !screeningDateInput.value) {
                return;
            }

            const birthDate = new Date(birthDateInput.value);
            const screeningDate = new Date(screeningDateInput.value);
            let months = (screeningDate.getFullYear() - birthDate.getFullYear()) * 12;
            months += screeningDate.getMonth() - birthDate.getMonth();

            if (screeningDate.getDate() < birthDate.getDate()) {
                months -= 1;
            }

            ageMonthsInput.value = Math.max(months, 0);
        }

        screeningDateInput.addEventListener('change', updateAgeMonths);
        updateAgeMonths();
    </script>
@endsection
