<?php

namespace App\Services;

use App\Models\Screening;
use App\Models\ScreeningResult;
use Illuminate\Support\Str;

class NutritionExpertSystemService
{
    public function evaluate(Screening $screening): array
    {
        $totalScore = 0;
        $triggeredRules = [];
        $explanations = [];
        $recommendations = [];
        $urgent = false;

        $addRule = function (string $code, string $explanation, string $recommendation, int $score = 1) use (&$totalScore, &$triggeredRules, &$explanations, &$recommendations): void {
            if (! in_array($code, $triggeredRules, true)) {
                $triggeredRules[] = $code;
            }

            $totalScore += $score;
            $explanations[] = $explanation;
            $recommendations[] = $recommendation;
        };

        if (! is_null($screening->muac_cm) && (float) $screening->muac_cm < 11.5) {
            $urgent = true;
            $triggeredRules[] = 'R8';
            $explanations[] = 'Sistem menandai rujukan segera karena MUAC kurang dari 11.5 cm, yang merupakan tanda risiko gizi akut berat.';
            $recommendations[] = 'Segera rujuk anak ke fasilitas kesehatan untuk pemeriksaan dan tata laksana oleh tenaga kesehatan.';
        }

        if (! is_null($screening->height_for_age_z_score)) {
            $heightForAgeZScore = (float) $screening->height_for_age_z_score;

            if ($heightForAgeZScore <= -3.00) {
                $urgent = true;
                $triggeredRules[] = 'R6';
                $explanations[] = 'Sistem menandai risiko stunting berat karena nilai HAZ manual kurang dari atau sama dengan -3.00 SD.';
                $recommendations[] = 'Sarankan evaluasi segera oleh tenaga kesehatan dan pemantauan pertumbuhan intensif.';
            } elseif ($heightForAgeZScore <= -2.00) {
                $addRule(
                    'R5',
                    'Sistem menandai risiko stunting berdasarkan rule Decision Tree hasil training: HAZ kurang dari atau sama dengan -2.00 SD.',
                    'Berikan edukasi gizi, pantau pertumbuhan, dan sarankan konsultasi ke tenaga kesehatan untuk penilaian lebih lanjut.',
                    4,
                );
            }
        }

        // Placeholder z-score manual sampai perhitungan WHO z-score otomatis ditambahkan.
        if (! is_null($screening->weight_for_height_z_score) && (float) $screening->weight_for_height_z_score < -3) {
            $urgent = true;
            $triggeredRules[] = 'R8';
            $explanations[] = 'Sistem menandai rujukan segera karena nilai WHZ/WLZ manual kurang dari -3 SD.';
            $recommendations[] = 'Segera konsultasikan hasil pengukuran ke fasilitas kesehatan untuk konfirmasi dan penanganan.';
        }

        if ($this->containsDangerSign($screening)) {
            $urgent = true;
            $triggeredRules[] = 'R8';
            $explanations[] = 'Sistem menemukan tanda bahaya pada catatan seperti lemah, tidak mau makan, atau edema.';
            $recommendations[] = 'Jangan menunda pemeriksaan. Bawa anak ke fasilitas kesehatan bila ada tanda bahaya.';
        }

        if (! is_null($screening->birth_weight_gram) && $screening->birth_weight_gram < 2500) {
            $addRule(
                'R19',
                'Sistem menandai risiko karena anak memiliki riwayat berat lahir rendah, yang berhubungan dengan peningkatan risiko masalah pertumbuhan.',
                'Lakukan pemantauan pertumbuhan di posyandu dan konsultasikan ke ahli gizi atau fasilitas kesehatan bila pertumbuhan tidak sesuai kurva.',
            );
        }

        if ($screening->is_premature) {
            $addRule(
                'R20',
                'Sistem menandai risiko karena anak memiliki riwayat lahir prematur, sehingga pertumbuhan perlu dipantau lebih hati-hati.',
                'Pastikan pemantauan pertumbuhan dilakukan berkala dan diskusikan riwayat prematur saat kontrol kesehatan.',
            );
        }

        if ($screening->has_recurrent_diarrhea) {
            $addRule(
                'R21',
                'Sistem menandai risiko karena ada riwayat diare berulang yang dapat mengganggu asupan dan penyerapan zat gizi.',
                'Perhatikan kecukupan cairan dan asupan saat sakit, serta konsultasikan bila diare berulang.',
            );
        }

        if ($screening->has_recurrent_infection) {
            $addRule(
                'R21',
                'Sistem menandai risiko karena ada riwayat infeksi berulang yang dapat memengaruhi pertumbuhan anak.',
                'Konsultasikan riwayat infeksi berulang ke tenaga kesehatan dan pantau pertumbuhan secara rutin.',
            );
        }

        if ($screening->food_insecurity) {
            $addRule(
                'R23',
                'Sistem menandai risiko karena keluarga memiliki ketahanan pangan rendah.',
                'Prioritaskan pangan bergizi terjangkau dan manfaatkan layanan dukungan pangan atau gizi bila tersedia.',
            );
        }

        if ($screening->safe_drinking_water === false) {
            $addRule(
                'R24',
                'Sistem menandai risiko karena air minum dilaporkan tidak aman, yang dapat meningkatkan risiko infeksi.',
                'Gunakan air minum yang dimasak atau diolah dengan aman dan jaga kebersihan wadah air.',
            );
        }

        if ($screening->proper_sanitation === false) {
            $addRule(
                'R25',
                'Sistem menandai risiko karena sanitasi belum layak, yang dapat meningkatkan paparan penyakit infeksi.',
                'Tingkatkan praktik sanitasi dasar, cuci tangan pakai sabun, dan kebersihan makanan anak.',
            );
        }

        if ($screening->age_months < 6 && $screening->exclusive_breastfeeding === false) {
            $addRule(
                'R11',
                'Sistem menandai risiko karena anak berusia di bawah 6 bulan dan tidak mendapat ASI eksklusif.',
                'Konsultasikan praktik pemberian makan bayi ke tenaga kesehatan agar kebutuhan gizi anak terpenuhi.',
            );
        }

        if ($screening->age_months >= 7 && $screening->complementary_feeding_started === false) {
            $addRule(
                'MPASI_DELAYED',
                'Sistem menandai risiko karena anak berusia 7 bulan atau lebih tetapi belum mulai MPASI.',
                'Diskusikan pemberian MPASI sesuai usia dengan kader posyandu, ahli gizi, atau tenaga kesehatan.',
            );
        }

        if ($screening->age_months >= 6 && $screening->age_months <= 8 && ! is_null($screening->meal_frequency_per_day) && $screening->meal_frequency_per_day < 2) {
            $addRule(
                'R14',
                'Sistem menandai risiko karena frekuensi makan anak usia 6 sampai 8 bulan kurang dari kebutuhan minimum.',
                'Tingkatkan frekuensi MPASI bertahap sesuai usia dan kemampuan makan anak.',
            );
        }

        if ($screening->age_months >= 9 && $screening->age_months <= 24 && ! is_null($screening->meal_frequency_per_day) && $screening->meal_frequency_per_day < 3) {
            $addRule(
                'R15',
                'Sistem menandai risiko karena frekuensi makan anak usia 9 sampai 24 bulan kurang dari kebutuhan minimum.',
                'Upayakan frekuensi makan dan camilan sehat sesuai usia untuk mendukung kecukupan energi.',
            );
        }

        if ($screening->age_months >= 6 && ! is_null($screening->dietary_diversity_score) && $screening->dietary_diversity_score < 5) {
            $addRule(
                'DIETARY_DIVERSITY_LOW',
                'Sistem menandai risiko karena skor keragaman pangan kurang dari 5.',
                'Variasikan makanan anak dengan sumber karbohidrat, protein, sayur, buah, dan pangan kaya mikronutrien sesuai usia.',
            );
        }

        if (in_array($screening->animal_protein_frequency, ['never', 'rare'], true)) {
            $addRule(
                'R17',
                'Sistem menandai risiko karena konsumsi protein hewani jarang atau tidak pernah.',
                'Tambahkan protein hewani yang sesuai usia dan terjangkau, seperti telur, ikan, ayam, hati, atau sumber lokal lainnya.',
            );
        }

        $riskCategory = $this->determineRiskCategory($urgent, $totalScore);

        if ($explanations === []) {
            $explanations[] = 'Tidak ada faktor risiko utama yang aktif berdasarkan data screening yang dimasukkan.';
        }

        if ($recommendations === []) {
            $recommendations[] = 'Lanjutkan pemantauan pertumbuhan rutin di posyandu dan pertahankan pola makan bergizi seimbang sesuai usia.';
        }

        return [
            'risk_category' => $riskCategory,
            'total_score' => $totalScore,
            'triggered_rules' => array_values(array_unique($triggeredRules)),
            'explanations' => array_values(array_unique($explanations)),
            'recommendations' => implode("\n", array_values(array_unique($recommendations))),
            'summary' => $this->buildSummary($riskCategory, $totalScore, $triggeredRules),
        ];
    }

    public function saveResult(Screening $screening): ScreeningResult
    {
        $result = $this->evaluate($screening);

        return ScreeningResult::updateOrCreate(
            ['screening_id' => $screening->id],
            $result,
        );
    }

    private function containsDangerSign(Screening $screening): bool
    {
        $notes = Str::lower($screening->notes ?? '');

        return $screening->has_edema
            || Str::contains($notes, ['lemah', 'tidak mau makan', 'edema']);
    }

    private function determineRiskCategory(bool $urgent, int $totalScore): string
    {
        if ($urgent) {
            return 'urgent';
        }

        if ($totalScore >= 4) {
            return 'high';
        }

        if ($totalScore >= 2) {
            return 'medium';
        }

        return 'low';
    }

    private function buildSummary(string $riskCategory, int $totalScore, array $triggeredRules): string
    {
        $labels = [
            'low' => 'Risiko rendah',
            'medium' => 'Risiko sedang',
            'high' => 'Risiko tinggi',
            'urgent' => 'Rujukan segera',
        ];

        $ruleCount = count(array_unique($triggeredRules));

        return "{$labels[$riskCategory]} dengan total skor {$totalScore}. Sistem mengaktifkan {$ruleCount} aturan berdasarkan data screening.";
    }
}
