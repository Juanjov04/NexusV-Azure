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
    Schema::create('enrollments', function (Blueprint $table) {
        $table->id();
        
        // --- COLUMNAS EXISTENTES ---
        $table->foreignId('course_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->unique(['course_id', 'user_id']); 
        $table->string('status')->default('pending'); // Cambiado a 'pending' por defecto para el flujo de pago
        // -----------------------------

        // --- NUEVAS COLUMNAS PARA CERTIFICADO ---
        $table->uuid('certificate_uuid')->nullable()->unique(); // UUID único para verificación pública (QR)
        $table->timestamp('completed_at')->nullable(); // Fecha de finalización (para el certificado)
        // ------------------------------------------
        
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
