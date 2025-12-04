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
        Schema::table('users', function (Blueprint $table) {
            // AÑADIR: Agrega la columna 'role' como string, con valor por defecto 'buyer', 
            // y la coloca justo después de la columna 'email'.
            $table->string('role')->default('buyer')->after('email'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // REVERTIR: Elimina la columna 'role' si se revierte la migración.
            $table->dropColumn('role');
        });
    }
};