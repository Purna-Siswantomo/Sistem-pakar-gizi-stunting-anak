<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('screening_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('screening_id')->constrained('screenings')->cascadeOnDelete();
            $table->enum('risk_category', ['low', 'medium', 'high', 'urgent']);
            $table->integer('total_score')->default(0);
            $table->text('summary');
            $table->text('recommendations');
            $table->json('explanations')->nullable();
            $table->json('triggered_rules')->nullable();
            $table->timestamps();

            $table->unique('screening_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('screening_results');
    }
};
