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
        // Modifier la table users pour utiliser UUID
        Schema::table('users', function (Blueprint $table) {
            $table->dropPrimary();
            $table->string('id')->change();
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir aux IDs auto-incrémentés
        Schema::table('users', function (Blueprint $table) {
            $table->dropPrimary();
            $table->id()->change();
            $table->primary('id');
        });
    }
};
