<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Screening extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'screening_date',
        'age_months',
        'weight_kg',
        'height_cm',
        'muac_cm',
        'height_for_age_z_score',
        'weight_for_height_z_score',
        'birth_weight_gram',
        'is_premature',
        'has_edema',
        'exclusive_breastfeeding',
        'complementary_feeding_started',
        'complementary_feeding_age_month',
        'meal_frequency_per_day',
        'dietary_diversity_score',
        'animal_protein_frequency',
        'has_recurrent_diarrhea',
        'has_recurrent_infection',
        'immunization_complete',
        'food_insecurity',
        'safe_drinking_water',
        'proper_sanitation',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'screening_date' => 'date',
            'weight_kg' => 'decimal:2',
            'height_cm' => 'decimal:2',
            'muac_cm' => 'decimal:1',
            'height_for_age_z_score' => 'decimal:2',
            'weight_for_height_z_score' => 'decimal:2',
            'is_premature' => 'boolean',
            'has_edema' => 'boolean',
            'exclusive_breastfeeding' => 'boolean',
            'complementary_feeding_started' => 'boolean',
            'has_recurrent_diarrhea' => 'boolean',
            'has_recurrent_infection' => 'boolean',
            'immunization_complete' => 'boolean',
            'food_insecurity' => 'boolean',
            'safe_drinking_water' => 'boolean',
            'proper_sanitation' => 'boolean',
        ];
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    public function result(): HasOne
    {
        return $this->hasOne(ScreeningResult::class);
    }
}
