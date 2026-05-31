<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Hasil Screening NutriScreen-ES</title>
    <style>
        body {
            color: #1f2937;
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }

        h1, h2, h3 {
            color: #111827;
            margin: 0;
        }

        h1 {
            font-size: 22px;
            margin-bottom: 4px;
        }

        h2 {
            border-bottom: 1px solid #d1d5db;
            font-size: 15px;
            margin-bottom: 10px;
            padding-bottom: 5px;
        }

        h3 {
            font-size: 13px;
            margin-bottom: 4px;
        }

        .muted {
            color: #6b7280;
        }

        .section {
            margin-top: 18px;
        }

        .grid {
            width: 100%;
        }

        .grid td {
            padding: 4px 6px 4px 0;
            vertical-align: top;
        }

        .label {
            color: #6b7280;
            width: 170px;
        }

        .badge {
            border: 1px solid #9ca3af;
            border-radius: 12px;
            display: inline-block;
            font-weight: bold;
            padding: 3px 10px;
        }

        .rule {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            margin-bottom: 10px;
            padding: 10px;
        }

        ul {
            margin-bottom: 0;
            margin-top: 6px;
            padding-left: 18px;
        }

        .disclaimer {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 6px;
            margin-top: 18px;
            padding: 10px;
        }
    </style>
</head>
<body>
    @php
        $riskLabels = [
            'low' => 'Risiko Rendah',
            'medium' => 'Risiko Sedang',
            'high' => 'Risiko Tinggi',
            'urgent' => 'Rujukan Segera',
        ];
        $recommendations = $screening->result
            ? array_values(array_filter(preg_split('/\r\n|\r|\n/', $screening->result->recommendations)))
            : [];
    @endphp

    <h1>Hasil Screening NutriScreen-ES</h1>
    <p class="muted">Dokumen hasil screening awal risiko stunting dan malnutrisi anak.</p>

    <div class="section">
        <h2>1. Identitas Anak</h2>
        <table class="grid">
            <tr><td class="label">Nama</td><td>{{ $screening->child->name }}</td></tr>
            <tr><td class="label">Jenis Kelamin</td><td>{{ $screening->child->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
            <tr><td class="label">Usia Saat Screening</td><td>{{ $screening->age_months }} bulan</td></tr>
            <tr><td class="label">Tanggal Screening</td><td>{{ $screening->screening_date->format('d M Y') }}</td></tr>
        </table>
    </div>

    <div class="section">
        <h2>2. Data Screening</h2>
        <table class="grid">
            <tr><td class="label">Berat Badan</td><td>{{ $screening->weight_kg }} kg</td></tr>
            <tr><td class="label">Tinggi/Panjang Badan</td><td>{{ $screening->height_cm }} cm</td></tr>
            <tr><td class="label">MUAC</td><td>{{ $screening->muac_cm ? $screening->muac_cm.' cm' : '-' }}</td></tr>
            <tr><td class="label">Berat Lahir</td><td>{{ $screening->birth_weight_gram ? $screening->birth_weight_gram.' gram' : '-' }}</td></tr>
            <tr><td class="label">Catatan</td><td>{{ $screening->notes ?: '-' }}</td></tr>
        </table>
    </div>

    <div class="section">
        <h2>3. Hasil Sistem</h2>
        @if ($screening->result)
            <table class="grid">
                <tr>
                    <td class="label">Kategori Risiko</td>
                    <td><span class="badge">{{ $riskLabels[$screening->result->risk_category] ?? strtoupper($screening->result->risk_category) }}</span></td>
                </tr>
                <tr><td class="label">Total Score</td><td>{{ $screening->result->total_score }}</td></tr>
                <tr><td class="label">Ringkasan</td><td>{{ $screening->result->summary }}</td></tr>
            </table>
        @else
            <p>Hasil sistem belum tersedia.</p>
        @endif
    </div>

    <div class="section">
        <h2>4. Rules yang Aktif</h2>
        @forelse ($triggeredRuleDetails as $rule)
            <div class="rule">
                <h3>{{ $rule['code'] }} - {{ $rule['name'] }}</h3>
                <p class="muted">Severity: {{ ucfirst($rule['severity']) }}</p>
                <p><strong>Explanation:</strong> {{ $rule['explanation'] }}</p>
                <p><strong>Recommendation:</strong> {{ $rule['recommendation'] }}</p>
            </div>
        @empty
            <p>Tidak ada rule risiko yang aktif dari data screening ini.</p>
        @endforelse
    </div>

    <div class="section">
        <h2>5. Explanation</h2>
        @if ($screening->result?->explanations)
            <ul>
                @foreach ($screening->result->explanations as $explanation)
                    <li>{{ $explanation }}</li>
                @endforeach
            </ul>
        @else
            <p>Tidak ada explanation tambahan.</p>
        @endif
    </div>

    <div class="section">
        <h2>6. Rekomendasi</h2>
        @if ($recommendations)
            <ul>
                @foreach ($recommendations as $recommendation)
                    <li>{{ $recommendation }}</li>
                @endforeach
            </ul>
        @else
            <p>Tidak ada rekomendasi tambahan.</p>
        @endif
    </div>

    <div class="disclaimer">
        <strong>Disclaimer:</strong>
        Hasil ini merupakan screening awal dan tidak menggantikan diagnosis dari ahli gizi, dokter, atau tenaga kesehatan. Jika anak masuk kategori risiko sedang, tinggi, atau rujukan segera, pengguna disarankan berkonsultasi ke fasilitas kesehatan.
    </div>
</body>
</html>
