<?php

namespace Tests\Feature;

use App\Models\Child;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAndScreeningHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_summary_counts(): void
    {
        $screening = $this->createScreeningWithResult('Alya', 'high');

        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Total Anak Terdaftar')
            ->assertSee('Total Screening Dilakukan')
            ->assertSee('Risiko Tinggi')
            ->assertSee($screening->child->name);
    }

    public function test_screening_history_can_filter_by_risk_category_and_child_name(): void
    {
        $this->createScreeningWithResult('Alya', 'high');
        $this->createScreeningWithResult('Bima', 'low');

        $this->actingAs(User::factory()->create())
            ->get(route('screenings.index', [
            'risk_category' => 'high',
            'search' => 'Alya',
            ]))
            ->assertOk()
            ->assertSee('Alya')
            ->assertSee('Risiko Tinggi')
            ->assertDontSee('Bima');
    }

    private function createScreeningWithResult(string $name, string $riskCategory)
    {
        $child = Child::create([
            'name' => $name,
            'gender' => 'female',
            'birth_date' => '2024-01-01',
        ]);

        $screening = $child->screenings()->create([
            'screening_date' => '2026-05-31',
            'age_months' => 29,
            'weight_kg' => 10.5,
            'height_cm' => 84,
            'is_premature' => false,
            'has_edema' => false,
            'has_recurrent_diarrhea' => false,
            'has_recurrent_infection' => false,
            'food_insecurity' => false,
        ]);

        $screening->result()->create([
            'risk_category' => $riskCategory,
            'total_score' => $riskCategory === 'high' ? 4 : 0,
            'summary' => 'Ringkasan hasil uji.',
            'recommendations' => 'Rekomendasi edukatif.',
            'explanations' => ['Explanation uji.'],
            'triggered_rules' => [],
        ]);

        return $screening->load(['child', 'result']);
    }
}
