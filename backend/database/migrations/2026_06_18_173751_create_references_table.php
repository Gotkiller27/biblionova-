<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('references', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('abstract')->nullable();
            $table->string('isbn')->nullable();
            $table->year('publication_year')->nullable();
            $table->enum('language', ['fr', 'en', 'other'])->default('en');
            $table->enum('document_type', ['livre', 'memoire', 'these', 'article', 'revue', 'rapport', 'guide', 'autre'])->default('livre');
            $table->foreignId('category_id')->constrained()->cascadeOnDelete()->index();
            $table->unsignedBigInteger('publisher_id')->nullable()->index();
            // Explicit constraint name prevents MySQL duplicate constraint issues on repeated migration runs.
            $table->foreign('publisher_id', 'references_publisher_id_foreign')
                ->references('id')
                ->on('publishers')
                ->nullOnDelete();

            $table->foreignId('uploaded_by')->constrained('users')->nullOnDelete()->index();
            $table->foreignId('bibliothecaire_id')->nullable()->constrained('users')->nullOnDelete()->index();
            $table->string('cover_image')->nullable();
            $table->string('file_path')->nullable();
            $table->integer('pages')->nullable();
            $table->unsignedBigInteger('download_count')->default(0);
            $table->unsignedBigInteger('view_count')->default(0);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('references');
    }
};
