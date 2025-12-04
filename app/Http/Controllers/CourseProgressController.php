<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\CourseProgress;
use App\Models\Course; // Importación necesaria para el Type Hinting en certify
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Models\Enrollment; // Añadimos la importación de Enrollment para usarlo de forma explícita
use Illuminate\Support\Str;

class CourseProgressController extends Controller
{
    // Constructor con middleware 'auth' ya que es un endpoint de usuario
    public function __construct()
    {
        $this->middleware('auth'); 
    }

    /**
     * Registra o actualiza el progreso de un usuario en un módulo.
     */
    public function store(Module $module): RedirectResponse
    {
        $user = Auth::user();

        // 1. Autorización: Asegurar que el usuario esté inscrito en el curso antes de registrar progreso.
        $isEnrolled = $user->enrollments()->where('course_id', $module->course_id)->exists();
        
        if (! $isEnrolled) {
            return back()->with('error', 'Debes estar inscrito para registrar tu progreso.');
        }

        // 2. Lógica de Registro: Usar updateOrCreate para evitar duplicados
        CourseProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'module_id' => $module->id,
            ],
            [
                'is_completed' => true // Marcamos como completado
            ]
        );

        return back()->with('success', '¡Lección marcada como completada! Buen trabajo.');
    }


    /**
     * Verifica el progreso y genera el certificado si el curso está 100% completado.
     */
    public function certify(Course $course)
    {
        $user = Auth::user();

        // 1. Verificar Inscripción y obtener la instancia de Enrollment (CRUCIAL)
        $enrollment = Enrollment::where('user_id', $user->id)
                            ->where('course_id', $course->id)
                            ->first(); 
        
        if (!$enrollment) {
            abort(403, 'Debes estar inscrito para obtener un certificado.');
        }

        // 2. Cargar Módulos y calcular Progreso (Lógica existente)
        $modules = $course->modules()
            ->with(['progress' => function ($query) use ($user) {
                $query->where('user_id', $user->id); 
            }])
            ->get();

        $totalModules = $modules->count();
        
        if ($totalModules === 0) {
            return back()->with('error', 'El curso no tiene módulos definidos.');
        }

        $completedModules = $modules->filter(fn($m) => 
            $m->progress->isNotEmpty() && $m->progress->first()->is_completed
        )->count();
        
        $progressPercent = round(($completedModules / $totalModules) * 100);

        // 3. Verificación de Finalización (100%)
        if ($progressPercent < 100) {
            return back()->with('error', "No has completado el curso al 100%. Progreso actual: {$progressPercent}%.");
        }

        // 4. Generar o Asegurar el Certificado (¡NUEVA LÓGICA!)
        if (!$enrollment->hasCertificate()) { 
            // Generar un UUID único para el enlace público
            $enrollment->certificate_uuid = Str::uuid(); 
            $enrollment->completed_at = now();
            $enrollment->save();
        }

        // 5. Mostrar Certificado (Pasamos el enrollment a la vista)
        return view('courses.certificate', compact('course', 'user', 'enrollment'));
    }
}