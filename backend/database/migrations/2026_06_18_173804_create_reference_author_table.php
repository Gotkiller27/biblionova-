<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reference_author', function (Blueprint $table) {
            $table->foreignId('reference_id')->constrained()->onDelete('cascade');
            $table->foreignId('author_id')->constrained()->onDelete('cascade');
            $table->primary(['reference_id', 'author_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reference_author');
    }
};
