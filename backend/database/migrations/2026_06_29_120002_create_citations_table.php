<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('citing_reference_id')->constrained('references')->cascadeOnDelete();
            $table->foreignId('cited_reference_id')->constrained('references')->cascadeOnDelete();
            $table->text('context')->nullable();
            $table->string('citation_style')->default('apa');
            $table->integer('page_number')->nullable();
            $table->timestamps();
            
            // Unique constraint pour éviter les doublons
            $table->unique(['citing_reference_id', 'cited_reference_id']);
            
            // Index pour la recherche
            $table->index(['citing_reference_id', 'created_at']);
            $table->index('cited_reference_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citations');
    }
};
