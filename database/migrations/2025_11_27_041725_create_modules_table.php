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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            // Llave foránea para vincular el módulo al curso
            $table->foreignId('course_id')->constrained()->onDelete('cascade'); 
            
            $table->string('title');
            
            // CORRECCIÓN AQUÍ: Agregamos ->nullable()
            // Esto permite que el campo esté vacío cuando subes un video o documento
            $table->text('content_url')->nullable(); 
            
            $table->integer('sequence_order')->default(1); // Para ordenar las lecciones
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};