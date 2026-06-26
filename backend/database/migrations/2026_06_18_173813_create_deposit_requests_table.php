<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deposit_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained('users')->cascadeOnDelete()->index();
            $table->foreignId('assigned_manager_id')->nullable()->constrained('users')->nullOnDelete()->index();
            $table->string('title');
            $table->text('description');
            $table->string('proposed_file')->nullable();
            $table->enum('status', ['pending', 'approved_by_manager', 'rejected_by_manager', 'second_review', 'approved', 'rejected', 'published'])->default('pending')->index();
            $table->timestamps();
            
            // Ajouter des index nommés pour éviter les conflits
            $table->index(['status', 'created_at'], 'deposit_requests_status_created_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposit_requests');
    }
};
