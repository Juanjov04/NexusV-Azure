<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            // Enumeración para definir el tipo de contenido: link, video, document
            $table->enum('content_type', ['link', 'video', 'document'])->default('link')->after('course_id'); 
            
            // Ruta del archivo en el servidor/cloud (para video/documento)
            $table->string('content_path')->nullable()->after('content_type');
            
            // Renombrar 'content' (asumiendo que es donde guardas el link) a 'link_url'
            // O si lo usas para el contenido de texto, mantenerlo.
            // Para simplificar, asumiremos que tu campo 'content' actual es el link/texto:
            // Si deseas mantener el campo de link original:
            // $table->text('link_url')->nullable()->change(); // Ajustar el nombre si es necesario
        });
    }

    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn('content_type');
            $table->dropColumn('content_path');
        });
    }
};