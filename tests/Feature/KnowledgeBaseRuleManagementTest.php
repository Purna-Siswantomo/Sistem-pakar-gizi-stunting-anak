<?php

namespace Tests\Feature;

use App\Models\Rule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KnowledgeBaseRuleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_rule_can_be_created(): void
    {
        $response = $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->post(route('knowledge-base.rules.store'), [
            'code' => 'R99',
            'name' => 'Rule Uji',
            'description' => 'Deskripsi rule uji.',
            'condition_summary' => 'Kondisi rule uji.',
            'recommendation' => 'Rekomendasi edukatif rule uji.',
            'explanation' => 'Explanation rule uji.',
            'severity' => 'medium',
            'source_reference' => 'Pedoman uji knowledge base.',
            'is_active' => '1',
            ]);

        $rule = Rule::firstWhere('code', 'R99');

        $response->assertRedirect(route('knowledge-base.rules.show', $rule));
        $this->assertDatabaseHas('rules', [
            'code' => 'R99',
            'name' => 'Rule Uji',
            'severity' => 'medium',
            'is_active' => 1,
        ]);
    }

    public function test_rule_can_be_updated_and_deactivated(): void
    {
        $rule = Rule::create([
            'code' => 'R98',
            'name' => 'Rule Lama',
            'description' => 'Deskripsi lama.',
            'condition_summary' => 'Kondisi lama.',
            'recommendation' => 'Rekomendasi lama.',
            'explanation' => 'Explanation lama.',
            'severity' => 'low',
            'source_reference' => 'Sumber lama.',
            'is_active' => true,
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->put(route('knowledge-base.rules.update', $rule), [
            'code' => 'R98',
            'name' => 'Rule Baru',
            'description' => 'Deskripsi baru.',
            'condition_summary' => 'Kondisi baru.',
            'recommendation' => 'Rekomendasi baru.',
            'explanation' => 'Explanation baru.',
            'severity' => 'high',
            'source_reference' => 'Sumber baru.',
            'is_active' => '1',
            ])->assertRedirect(route('knowledge-base.rules.show', $rule));

        $this->assertDatabaseHas('rules', [
            'id' => $rule->id,
            'name' => 'Rule Baru',
            'severity' => 'high',
        ]);

        $this->actingAs($admin)
            ->patch(route('knowledge-base.rules.deactivate', $rule))
            ->assertRedirect(route('knowledge-base.rules'));

        $this->assertDatabaseHas('rules', [
            'id' => $rule->id,
            'is_active' => 0,
        ]);
    }
}
