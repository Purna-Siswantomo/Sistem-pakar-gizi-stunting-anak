<?php

namespace Tests\Feature;

use App\Models\Child;
use App\Services\NutritionExpertSystemService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NutritionExpertSystemServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_muac_below_threshold_returns_urgent(): void
    {
        $screening = $this->createScreening([
            'muac_cm' => 11.4,
            'notes' => null,
        ]);

        $result = app(NutritionExpertSystemService::class)->evaluate($screening);

        $this->assertSame('urgent', $result['risk_category']);
        $this->assertContains('R8', $result['triggered_rules']);
    }

    public function test_multiple_risk_factors_return_high_category(): void
    {
        $screening = $this->createScreening([
            'birth_weight_gram' => 2400,
            'is_premature' => true,
            'has_recurrent_diarrhea' => true,
            'food_insecurity' => true,
        ]);

        $result = app(NutritionExpertSystemService::class)->evaluate($screening);

        $this->assertSame('high', $result['risk_category']);
        $this->assertSame(4, $result['total_score']);
        $this->assertContains('R19', $result['triggered_rules']);
        $this->assertContains('R20', $result['triggered_rules']);
        $this->assertContains('R21', $result['triggered_rules']);
        $this->assertContains('R23', $result['triggered_rules']);
    }

    public function test_ml_stunting_rule_uses_height_for_age_z_score_threshold(): void
    {
        $screening = $this->createScreening([
            'height_for_age_z_score' => -2.00,
        ]);

        $result = app(NutritionExpertSystemService::class)->evaluate($screening);

        $this->assertSame('high', $result['risk_category']);
        $this->assertSame(4, $result['total_score']);
        $this->assertContains('R5', $result['triggered_rules']);
    }

    public function test_severe_stunting_threshold_returns_urgent(): void
    {
        $screening = $this->createScreening([
            'height_for_age_z_score' => -3.00,
        ]);

        $result = app(NutritionExpertSystemService::class)->evaluate($screening);

        $this->assertSame('urgent', $result['risk_category']);
        $this->assertContains('R6', $result['triggered_rules']);
    }

    public function test_normal_height_for_age_z_score_does_not_trigger_stunting_rule(): void
    {
        $screening = $this->createScreening([
            'height_for_age_z_score' => -1.99,
        ]);

        $result = app(NutritionExpertSystemService::class)->evaluate($screening);

        $this->assertSame('low', $result['risk_category']);
        $this->assertNotContains('R5', $result['triggered_rules']);
        $this->assertNotContains('R6', $result['triggered_rules']);
    }

    private function createScreening(array $attributes = [])
    {
        $child = Child::create([
            'name' => 'Dina',
            'gender' => 'female',
            'birth_date' => '2024-01-01',
        ]);

        return $child->screenings()->create(array_merge([
            'screening_date' => '2026-05-31',
            'age_months' => 29,
            'weight_kg' => 10.5,
            'height_cm' => 84,
            'is_premature' => false,
            'has_edema' => false,
            'has_recurrent_diarrhea' => false,
            'has_recurrent_infection' => false,
            'food_insecurity' => false,
        ], $attributes));
    }
}
