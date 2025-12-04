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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // A quién pertenece la tarjeta
            
            // Datos simulados de la tarjeta
            $table->string('card_token')->unique(); // Token de pago simulado
            $table->string('brand')->nullable();     // Ejemplo: Visa, Mastercard
            $table->string('last_four', 4);          // Últimos 4 dígitos para visualización
            $table->date('expires_at');             // Fecha de expiración (para validación)
            $table->string('card_holder_name');
            $table->boolean('is_default')->default(false); // Tarjeta predeterminada para compras rápidas

            $table->timestamps();

            $table->unique(['user_id', 'is_default']); // Solo una tarjeta puede ser la predeterminada
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};