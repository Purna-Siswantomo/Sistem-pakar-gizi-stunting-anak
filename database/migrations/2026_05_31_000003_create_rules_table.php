<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rules', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description');
            $table->text('condition_summary');
            $table->text('recommendation');
            $table->text('explanation');
            $table->enum('severity', ['info', 'low', 'medium', 'high', 'urgent']);
            $table->boolean('is_active')->default(true);
            $table->text('source_reference')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rules');
    }
};
