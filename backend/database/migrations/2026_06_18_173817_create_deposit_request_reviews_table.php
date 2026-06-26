<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deposit_request_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deposit_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->enum('reviewer_role', ['responsable_demande', 'admin']);
            $table->enum('decision', ['approved', 'rejected', 'override', 'second_opinion_requested']);
            $table->text('justification')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposit_request_reviews');
    }
};
