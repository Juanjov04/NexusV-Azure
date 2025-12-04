<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('set null'); // Vincula al curso
            $table->string('transaction_id')->unique(); // El ID simulado
            $table->decimal('amount', 8, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['succeeded', 'failed', 'pending'])->default('pending');
            $table->string('payment_method')->default('Simulated Card');
            $table->json('payment_details')->nullable(); // Para guardar los últimos 4 dígitos
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};