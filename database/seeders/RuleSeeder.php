<?php

namespace Database\Seeders;

use App\Models\Rule;
use Illuminate\Database\Seeder;

class RuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            [
                'code' => 'R1',
                'name' => 'Validasi data antropometri',
                'description' => 'Memastikan data berat badan, tinggi/panjang badan, umur, dan pengukuran pendukung terisi secara wajar sebelum inferensi risiko.',
                'condition_summary' => 'Data antropometri kosong, bernilai tidak realistis, atau tidak konsisten dengan umur anak.',
                'recommendation' => 'Periksa ulang input pengukuran dan ulangi pengukuran jika diperlukan sebelum membaca kategori risiko.',
                'explanation' => 'Hasil screening bergantung pada kualitas data antropometri. Data yang tidak valid dapat menghasilkan rekomendasi yang keliru.',
                'severity' => 'info',
                'source_reference' => 'Prinsip validasi data antropometri sebelum interpretasi status gizi.',
            ],
            [
                'code' => 'R5',
                'name' => 'Risiko stunting',
                'description' => 'Menandai potensi stunting berdasarkan indikator tinggi/panjang badan menurut umur.',
                'condition_summary' => 'HAZ < -2 SD.',
                'recommendation' => 'Berikan edukasi gizi, pantau pertumbuhan, dan sarankan konsultasi ke tenaga kesehatan untuk penilaian lebih lanjut.',
                'explanation' => 'Nilai HAZ di bawah -2 SD menunjukkan anak lebih pendek dari standar pertumbuhan sesuai umur.',
                'severity' => 'high',
                'source_reference' => 'WHO Child Growth Standards: height-for-age z-score.',
            ],
            [
                'code' => 'R6',
                'name' => 'Risiko stunting berat',
                'description' => 'Menandai potensi stunting berat berdasarkan indikator tinggi/panjang badan menurut umur.',
                'condition_summary' => 'HAZ < -3 SD.',
                'recommendation' => 'Sarankan evaluasi segera oleh tenaga kesehatan dan pemantauan pertumbuhan intensif.',
                'explanation' => 'Nilai HAZ di bawah -3 SD menunjukkan defisit pertumbuhan linear yang berat.',
                'severity' => 'urgent',
                'source_reference' => 'WHO Child Growth Standards: severe stunting threshold.',
            ],
            [
                'code' => 'R7',
                'name' => 'Risiko wasting',
                'description' => 'Menandai potensi wasting berdasarkan berat badan terhadap tinggi/panjang badan.',
                'condition_summary' => 'WHZ/WLZ < -2 SD.',
                'recommendation' => 'Sarankan pemantauan asupan dan konsultasi dengan tenaga kesehatan untuk evaluasi status gizi.',
                'explanation' => 'Nilai WHZ/WLZ di bawah -2 SD dapat mengindikasikan kekurangan gizi akut.',
                'severity' => 'high',
                'source_reference' => 'WHO Child Growth Standards: weight-for-height/length z-score.',
            ],
            [
                'code' => 'R8',
                'name' => 'Rujukan segera gizi akut berat',
                'description' => 'Menandai kondisi yang perlu rujukan segera terkait risiko gizi akut berat.',
                'condition_summary' => 'WHZ/WLZ < -3 SD, MUAC < 11.5 cm, atau ditemukan edema.',
                'recommendation' => 'Rujuk segera ke fasilitas kesehatan untuk pemeriksaan dan tata laksana medis.',
                'explanation' => 'Ambang ini dapat menunjukkan kondisi gizi akut berat atau tanda bahaya yang membutuhkan penanganan profesional.',
                'severity' => 'urgent',
                'source_reference' => 'WHO/UNICEF criteria for severe acute malnutrition screening.',
            ],
            [
                'code' => 'R11',
                'name' => 'Tidak ASI eksklusif',
                'description' => 'Menandai riwayat tidak mendapatkan ASI eksklusif sebagai faktor risiko nutrisi awal.',
                'condition_summary' => 'exclusive_breastfeeding = false.',
                'recommendation' => 'Berikan edukasi praktik pemberian makan bayi dan anak sesuai umur.',
                'explanation' => 'Riwayat ASI eksklusif berkaitan dengan dukungan imunitas dan pemenuhan kebutuhan bayi pada awal kehidupan.',
                'severity' => 'medium',
                'source_reference' => 'WHO infant and young child feeding recommendations.',
            ],
            [
                'code' => 'R14',
                'name' => 'Frekuensi makan kurang usia 6 sampai 8 bulan',
                'description' => 'Menandai frekuensi makan yang kurang pada anak usia 6 sampai 8 bulan.',
                'condition_summary' => 'age_months 6-8 dan meal_frequency_per_day kurang dari kebutuhan minimum.',
                'recommendation' => 'Edukasi keluarga mengenai frekuensi MPASI yang sesuai usia 6 sampai 8 bulan.',
                'explanation' => 'Frekuensi makan yang kurang dapat mengurangi kecukupan energi dan zat gizi pada masa pertumbuhan cepat.',
                'severity' => 'medium',
                'source_reference' => 'WHO infant and young child feeding minimum meal frequency.',
            ],
            [
                'code' => 'R15',
                'name' => 'Frekuensi makan kurang usia 9 sampai 24 bulan',
                'description' => 'Menandai frekuensi makan yang kurang pada anak usia 9 sampai 24 bulan.',
                'condition_summary' => 'age_months 9-24 dan meal_frequency_per_day kurang dari kebutuhan minimum.',
                'recommendation' => 'Edukasi keluarga mengenai peningkatan frekuensi dan kualitas MPASI sesuai usia.',
                'explanation' => 'Pada usia 9 sampai 24 bulan, kebutuhan energi dari makanan pendamping meningkat sehingga frekuensi makan perlu memadai.',
                'severity' => 'medium',
                'source_reference' => 'WHO infant and young child feeding minimum meal frequency.',
            ],
            [
                'code' => 'R17',
                'name' => 'Protein hewani kurang',
                'description' => 'Menandai konsumsi protein hewani yang jarang atau tidak pernah.',
                'condition_summary' => 'animal_protein_frequency = never atau rare.',
                'recommendation' => 'Berikan edukasi pilihan protein hewani yang terjangkau dan sesuai usia anak.',
                'explanation' => 'Protein hewani membantu pemenuhan protein berkualitas dan mikronutrien penting untuk pertumbuhan.',
                'severity' => 'medium',
                'source_reference' => 'Guidance on complementary feeding and dietary diversity.',
            ],
            [
                'code' => 'R19',
                'name' => 'Berat lahir rendah',
                'description' => 'Menandai riwayat berat lahir rendah sebagai faktor risiko pertumbuhan.',
                'condition_summary' => 'birth_weight_gram < 2500.',
                'recommendation' => 'Pantau pertumbuhan lebih ketat dan sarankan konsultasi rutin ke tenaga kesehatan.',
                'explanation' => 'Berat lahir rendah berkaitan dengan risiko masalah pertumbuhan dan kebutuhan pemantauan lanjutan.',
                'severity' => 'medium',
                'source_reference' => 'WHO low birth weight threshold.',
            ],
            [
                'code' => 'R20',
                'name' => 'Prematur',
                'description' => 'Menandai riwayat lahir prematur sebagai faktor risiko pertumbuhan.',
                'condition_summary' => 'is_premature = true.',
                'recommendation' => 'Gunakan pemantauan pertumbuhan yang sesuai dan sarankan kontrol kesehatan berkala.',
                'explanation' => 'Anak dengan riwayat prematur dapat memerlukan interpretasi pertumbuhan dan pemantauan yang lebih hati-hati.',
                'severity' => 'medium',
                'source_reference' => 'Clinical growth monitoring considerations for preterm infants.',
            ],
            [
                'code' => 'R21',
                'name' => 'Diare atau infeksi berulang',
                'description' => 'Menandai riwayat diare atau infeksi berulang sebagai faktor risiko malnutrisi.',
                'condition_summary' => 'has_recurrent_diarrhea = true atau has_recurrent_infection = true.',
                'recommendation' => 'Edukasi kebersihan, asupan saat sakit, dan sarankan pemeriksaan kesehatan bila keluhan berulang.',
                'explanation' => 'Infeksi berulang dapat mengganggu asupan, penyerapan zat gizi, dan pertumbuhan anak.',
                'severity' => 'medium',
                'source_reference' => 'Nutrition and infection cycle in child malnutrition risk.',
            ],
            [
                'code' => 'R23',
                'name' => 'Food insecurity',
                'description' => 'Menandai risiko keterbatasan akses pangan keluarga.',
                'condition_summary' => 'food_insecurity = true.',
                'recommendation' => 'Berikan edukasi pemilihan pangan bergizi terjangkau dan arahkan ke dukungan sosial bila tersedia.',
                'explanation' => 'Keterbatasan akses pangan dapat menghambat pemenuhan kebutuhan energi dan zat gizi anak.',
                'severity' => 'medium',
                'source_reference' => 'Household food insecurity as nutrition risk factor.',
            ],
            [
                'code' => 'R24',
                'name' => 'Air minum tidak layak',
                'description' => 'Menandai penggunaan sumber air minum yang tidak aman sebagai faktor risiko lingkungan.',
                'condition_summary' => 'safe_drinking_water = false.',
                'recommendation' => 'Edukasi pengolahan air minum aman dan praktik higiene rumah tangga.',
                'explanation' => 'Air minum tidak layak meningkatkan risiko penyakit infeksi yang dapat memperburuk status gizi.',
                'severity' => 'medium',
                'source_reference' => 'WASH risk factors related to child nutrition.',
            ],
            [
                'code' => 'R25',
                'name' => 'Sanitasi tidak layak',
                'description' => 'Menandai sanitasi rumah tangga yang tidak memadai sebagai faktor risiko lingkungan.',
                'condition_summary' => 'proper_sanitation = false.',
                'recommendation' => 'Edukasi sanitasi dasar, cuci tangan pakai sabun, dan pencegahan kontaminasi makanan.',
                'explanation' => 'Sanitasi tidak layak dapat meningkatkan paparan patogen dan risiko infeksi berulang.',
                'severity' => 'medium',
                'source_reference' => 'WASH risk factors related to child nutrition.',
            ],
        ];

        foreach ($rules as $rule) {
            Rule::updateOrCreate(
                ['code' => $rule['code']],
                $rule + ['is_active' => true],
            );
        }
    }
}
