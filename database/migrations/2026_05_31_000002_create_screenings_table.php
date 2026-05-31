<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('screenings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('children')->cascadeOnDelete();
            $table->date('screening_date');
            $table->integer('age_months');
            $table->decimal('weight_kg', 5, 2);
            $table->decimal('height_cm', 5, 2);
            $table->decimal('muac_cm', 4, 1)->nullable();
            $table->decimal('height_for_age_z_score', 4, 2)->nullable();
            $table->decimal('weight_for_height_z_score', 4, 2)->nullable();
            $table->integer('birth_weight_gram')->nullable();
            $table->boolean('is_premature')->default(false);
            $table->boolean('has_edema')->default(false);
            $table->boolean('exclusive_breastfeeding')->nullable();
            $table->boolean('complementary_feeding_started')->nullable();
            $table->integer('complementary_feeding_age_month')->nullable();
            $table->integer('meal_frequency_per_day')->nullable();
            $table->integer('dietary_diversity_score')->nullable();
            $table->enum('animal_protein_frequency', ['never', 'rare', 'sometimes', 'often'])->nullable();
            $table->boolean('has_recurrent_diarrhea')->default(false);
            $table->boolean('has_recurrent_infection')->default(false);
            $table->boolean('immunization_complete')->nullable();
            $table->boolean('food_insecurity')->default(false);
            $table->boolean('safe_drinking_water')->nullable();
            $table->boolean('proper_sanitation')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('screenings');
    }
};
