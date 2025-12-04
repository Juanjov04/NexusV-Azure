<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    /**
     * Muestra la página de verificación del certificado a partir de su UUID.
     * @param string $uuid
     */
    public function verify(string $uuid)
    {
        // 1. Buscar el Enrollment usando el UUID
        $enrollment = Enrollment::with(['user', 'course.user'])
                            ->where('certificate_uuid', $uuid)
                            ->first();

        // 2. Manejo de error si no se encuentra
        if (!$enrollment) {
            return view('certificate.not-found', ['uuid' => $uuid]);
        }
        
        // 3. Mostrar la vista de verificación con los datos del certificado
        return view('certificate.verification', compact('enrollment'));
    }
}