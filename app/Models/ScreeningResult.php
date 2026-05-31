<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScreeningResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'screening_id',
        'risk_category',
        'total_score',
        'summary',
        'recommendations',
        'explanations',
        'triggered_rules',
    ];

    protected function casts(): array
    {
        return [
            'explanations' => 'array',
            'triggered_rules' => 'array',
        ];
    }

    public function screening(): BelongsTo
    {
        return $this->belongsTo(Screening::class);
    }
}
