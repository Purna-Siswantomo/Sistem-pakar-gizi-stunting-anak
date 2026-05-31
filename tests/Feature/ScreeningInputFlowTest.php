<?php

namespace Tests\Feature;

use App\Models\Child;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScreeningInputFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_child_can_be_created(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->post(route('children.store'), [
            'name' => 'Alya',
            'gender' => 'female',
            'birth_date' => '2024-01-15',
            'parent_name' => 'Ibu Alya',
            'address' => 'Jl. Contoh',
            ]);

        $child = Child::first();

        $response->assertRedirect(route('children.show', $child));
        $this->assertDatabaseHas('children', [
            'name' => 'Alya',
            'gender' => 'female',
        ]);
    }

    public function test_screening_can_be_created_for_child(): void
    {
        $child = Child::create([
            'name' => 'Bima',
            'gender' => 'male',
            'birth_date' => '2023-06-10',
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->post(route('children.screenings.store', $child), [
            'name' => 'Bima',
            'gender' => 'male',
            'birth_date' => '2023-06-10',
            'screening_date' => '2026-05-31',
            'age_months' => 35,
            'weight_kg' => 11.8,
            'height_cm' => 88.5,
            'muac_cm' => 12.4,
            'birth_weight_gram' => 2400,
            'is_premature' => 1,
            'exclusive_breastfeeding' => 0,
            'complementary_feeding_started' => 1,
            'complementary_feeding_age_month' => 6,
            'meal_frequency_per_day' => 2,
            'dietary_diversity_score' => 3,
            'animal_protein_frequency' => 'rare',
            'has_recurrent_diarrhea' => 1,
            'has_recurrent_infection' => 0,
            'immunization_complete' => 1,
            'food_insecurity' => 1,
            'safe_drinking_water' => 0,
            'proper_sanitation' => 0,
            'notes' => 'Data awal screening.',
            ]);

        $screening = $child->screenings()->first();

        $response->assertRedirect(route('screenings.show', $screening));
        $this->assertDatabaseHas('screenings', [
            'child_id' => $child->id,
            'age_months' => 35,
            'animal_protein_frequency' => 'rare',
            'food_insecurity' => 1,
        ]);
        $this->assertDatabaseHas('screening_results', [
            'screening_id' => $screening->id,
            'risk_category' => 'high',
        ]);
    }

    public function test_screening_result_can_be_exported_to_pdf(): void
    {
        $child = Child::create([
            'name' => 'Citra',
            'gender' => 'female',
            'birth_date' => '2024-02-01',
        ]);

        $screening = $child->screenings()->create([
            'screening_date' => '2026-05-31',
            'age_months' => 27,
            'weight_kg' => 10.2,
            'height_cm' => 83.5,
            'is_premature' => false,
            'has_edema' => false,
            'has_recurrent_diarrhea' => false,
            'has_recurrent_infection' => false,
            'food_insecurity' => false,
        ]);

        $screening->result()->create([
            'risk_category' => 'low',
            'total_score' => 0,
            'summary' => 'Risiko rendah dengan total skor 0.',
            'recommendations' => 'Lanjutkan pemantauan pertumbuhan rutin.',
            'explanations' => ['Tidak ada faktor risiko utama yang aktif.'],
            'triggered_rules' => [],
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->get(route('screenings.pdf', $screening));

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('content-type'));
    }
}
