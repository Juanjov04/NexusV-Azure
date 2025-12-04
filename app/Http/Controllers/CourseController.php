<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Muestra una lista de cursos publicados para los compradores, con búsqueda.
     */
    public function index(Request $request)
    {
        // Consulta solo cursos que han sido marcados como publicados
        $query = Course::with('user')
            ->where('is_published', true)
            ->latest('scheduled_date'); 

        // Implementar la búsqueda por título y encabezado
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('header', 'like', "%{$search}%");
            });
        }

        $courses = $query->paginate(12);

        return view('courses.public-index', compact('courses'));
    }


    /**
     * Muestra el contenido completo (módulos) de un curso para usuarios inscritos.
     */
    public function content(Course $course)
    {
        $user = Auth::user();

        // 1. Verificar Inscripción: Solo si el usuario está inscrito, puede ver el contenido.
        $isEnrolled = $user->enrollments()->where('course_id', $course->id)->exists();
        
        if (! $isEnrolled) {
            abort(403, 'Debes estar inscrito para acceder al contenido del curso.');
        }

        // 2. Cargar Módulos y Progreso:
        $modules = $course->modules()
                          ->with(['progress' => function ($query) use ($user) {
                              // Carga solo el progreso relevante para el usuario actual.
                              $query->where('user_id', $user->id);
                          }])
                          ->get();

        // 3. Cálculo de Progreso:
        $totalModules = $modules->count();
        $completedModules = $modules->filter(fn($m) => $m->progress->isNotEmpty() && $m->progress->first()->is_completed)->count();
        $progressPercent = ($totalModules > 0) ? round(($completedModules / $totalModules) * 100) : 0;
        
        return view('courses.content', compact('course', 'modules', 'progressPercent', 'completedModules', 'totalModules'));
    }


    /**
     * Muestra la vista de detalle de un curso.
     */
    public function show(Course $course)
    {
        // Redirección si el curso existe pero no está publicado
        if (!$course->is_published) {
            abort(404);
        }

        return view('courses.show', compact('course'));
    }
}