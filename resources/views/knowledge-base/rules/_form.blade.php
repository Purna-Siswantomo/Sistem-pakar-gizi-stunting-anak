@php
    $selectedSeverity = old('severity', $rule->severity ?? '');
    $isActive = old('is_active', isset($rule) ? (string) (int) $rule->is_active : '1');
@endphp

@csrf
@isset($method)
    @method($method)
@endisset

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                <input id="code" name="code" type="text" value="{{ old('code', $rule->code ?? '') }}" class="form-control" required>
            </div>
            <div class="col-md-5">
                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                <input id="name" name="name" type="text" value="{{ old('name', $rule->name ?? '') }}" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label for="severity" class="form-label">Severity <span class="text-danger">*</span></label>
                <select id="severity" name="severity" class="form-select" required>
                    <option value="">Pilih</option>
                    @foreach (['info', 'low', 'medium', 'high', 'urgent'] as $severity)
                        <option value="{{ $severity }}" @selected($selectedSeverity === $severity)>{{ ucfirst($severity) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" rows="3" class="form-control">{{ old('description', $rule->description ?? '') }}</textarea>
            </div>

            <div class="col-12">
                <label for="condition_summary" class="form-label">Condition Summary</label>
                <textarea id="condition_summary" name="condition_summary" rows="3" class="form-control">{{ old('condition_summary', $rule->condition_summary ?? '') }}</textarea>
            </div>

            <div class="col-lg-6">
                <label for="explanation" class="form-label">Explanation <span class="text-danger">*</span></label>
                <textarea id="explanation" name="explanation" rows="5" class="form-control" required>{{ old('explanation', $rule->explanation ?? '') }}</textarea>
            </div>

            <div class="col-lg-6">
                <label for="recommendation" class="form-label">Recommendation <span class="text-danger">*</span></label>
                <textarea id="recommendation" name="recommendation" rows="5" class="form-control" required>{{ old('recommendation', $rule->recommendation ?? '') }}</textarea>
            </div>

            <div class="col-12">
                <label for="source_reference" class="form-label">Source Reference <span class="text-danger">*</span></label>
                <textarea id="source_reference" name="source_reference" rows="3" class="form-control" required>{{ old('source_reference', $rule->source_reference ?? '') }}</textarea>
                <div class="form-text">Catat sumber ilmiah, standar, pedoman, atau rujukan ahli yang mendasari rule.</div>
            </div>

            <div class="col-12">
                <div class="form-check form-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input id="is_active" name="is_active" value="1" class="form-check-input" type="checkbox" @checked($isActive === '1')>
                    <label class="form-check-label" for="is_active">Rule aktif</label>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer bg-white d-flex justify-content-between">
        <a href="{{ route('knowledge-base.rules') }}" class="btn btn-outline-secondary">Kembali</a>
        <button type="submit" class="btn btn-success">Simpan Rule</button>
    </div>
</div>
