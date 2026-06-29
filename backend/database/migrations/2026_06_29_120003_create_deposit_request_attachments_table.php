<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deposit_request_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deposit_request_id')->constrained()->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->unsignedBigInteger('file_size');
            $table->string('mime_type')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Index pour la recherche
            $table->index(['deposit_request_id', 'created_at']);
            $table->index('file_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposit_request_attachments');
    }
};
