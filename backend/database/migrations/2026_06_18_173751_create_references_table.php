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
            
            // Clé étrangère Publisher (Déjà explicite)
            $table->foreign('publisher_id', 'references_publisher_id_foreign')
                ->references('id')
                ->on('publishers')
                ->nullOnDelete();

            // Clé étrangère Uploaded By (Sécurisée avec nom explicite)
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->foreign('uploaded_by', 'references_uploaded_by_foreign')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            
            // Clé étrangère Bibliothecaire (Sécurisée avec nom explicite)
            $table->unsignedBigInteger('bibliothecaire_id')->nullable();
            $table->foreign('bibliothecaire_id', 'references_bibliothecaire_id_foreign')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

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