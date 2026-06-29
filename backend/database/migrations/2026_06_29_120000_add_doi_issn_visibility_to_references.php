<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('references', function (Blueprint $table) {
            // DOI et ISSN
            $table->string('doi')->nullable()->after('isbn');
            $table->string('issn')->nullable()->after('doi');
            
            // Visibilité et disponibilité
            $table->enum('visibility', ['public', 'private', 'restricted'])->default('public')->after('status');
            $table->enum('availability', ['available', 'borrowed', 'reserved', 'unavailable'])->default('available')->after('visibility');
            
            // Index pour la recherche
            $table->index('visibility');
            $table->index('availability');
        });
    }

    public function down(): void
    {
        Schema::table('references', function (Blueprint $table) {
            $table->dropIndex(['visibility']);
            $table->dropIndex(['availability']);
            $table->dropColumn(['doi', 'issn', 'visibility', 'availability']);
        });
    }
};
