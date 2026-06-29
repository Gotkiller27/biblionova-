<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('keywords', function (Blueprint $table) {
            $table->integer('usage_count')->default(0)->after('name');
            $table->integer('popularity_score')->default(0)->after('usage_count');
            $table->text('description')->nullable()->after('popularity_score');
            $table->string('slug')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('keywords', function (Blueprint $table) {
            $table->dropColumn(['usage_count', 'popularity_score', 'description', 'slug']);
        });
    }
};
