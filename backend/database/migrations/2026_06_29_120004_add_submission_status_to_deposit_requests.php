<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deposit_requests', function (Blueprint $table) {
            // Ajouter un champ pour distinguer brouillon et soumis
            $table->enum('submission_status', ['draft', 'submitted'])->default('draft')->after('status');
            
            // Ajouter des champs pour le workflow complet
            $table->timestamp('submitted_at')->nullable()->after('submission_status');
            $table->timestamp('cancelled_at')->nullable()->after('submitted_at');
            $table->text('cancellation_reason')->nullable()->after('cancelled_at');
            
            // Index pour la recherche
            $table->index('submission_status');
            $table->index('submitted_at');
        });
    }

    public function down(): void
    {
        Schema::table('deposit_requests', function (Blueprint $table) {
            $table->dropIndex(['submission_status']);
            $table->dropIndex(['submitted_at']);
            $table->dropColumn(['submission_status', 'submitted_at', 'cancelled_at', 'cancellation_reason']);
        });
    }
};
