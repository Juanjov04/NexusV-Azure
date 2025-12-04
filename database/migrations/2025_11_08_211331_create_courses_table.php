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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            
            // Llave foránea que enlaza el curso con el vendedor (user_id)
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            
            $table->string('title'); // Título del curso
            $table->string('header'); // Encabezado/Subtítulo
            $table->text('description')->nullable(); // Una descripción más detallada
            $table->dateTime('scheduled_date'); // Fecha y hora de la clase (tentativa)
            $table->decimal('price', 8, 2)->default(0.00);
            $table->boolean('is_published')->default(false); // Para control de publicación
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};