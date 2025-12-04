<!DOCTYPE html>
<html lang="es">
<head>
    <title>Certificado de {{ $course->title }}</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; }
        .certificate-box { border: 10px solid #4a5568; padding: 50px; width: 80%; margin: 50px auto; }
        h1 { font-size: 2.5em; color: #4a5568; }
        p { font-size: 1.2em; margin-bottom: 30px; }
        .course-title { font-size: 1.8em; color: #38a169; font-weight: bold; }
        .verification-info { margin-top: 40px; border-top: 1px solid #ddd; padding-top: 20px; }
        .verification-info p { font-size: 0.9em; color: #666; }
    </style>
</head>
<body>
    <div class="certificate-box">
        <h1>CERTIFICADO DE FINALIZACIÓN</h1>
        <p>Se otorga a:</p>
        <h2>{{ $user->name }}</h2>
        <p>Por haber completado satisfactoriamente el curso:</p>
        <h3 class="course-title">{{ $course->title }}</h3>
        <p>En la plataforma NexusV-V2.</p>
        
        <p style="margin-top: 50px;">Fecha de Finalización: **{{ $enrollment->completed_at->format('d M Y') }}**</p>

        <div class="verification-info">
            <h3 style="font-size: 1.3em; margin-bottom: 15px; color: #4a5568;">Verificación de Autenticidad</h3>
            
            <p>Escanee el código QR para verificar este certificado en línea, o use el ID de verificación:</p>

            <div style="display: inline-block; margin: 15px auto;">
                {{-- Generación del QR code --}}
                {{-- Se asume que la ruta 'certificate.verify' y la librería Simple QR Code están configuradas --}}
                {!! QrCode::size(120)->generate(route('certificate.verify', ['uuid' => $enrollment->certificate_uuid])) !!}
            </div>
            
            <p style="margin-top: 15px;">
                ID de Verificación Único: **{{ $enrollment->certificate_uuid }}**
            </p>
            <p style="font-size: 0.8em;">
                Vendedor/Profesor: {{ $course->user->name }}
            </p>
        </div>
        
    </div>
</body>
</html>