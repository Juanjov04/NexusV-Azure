<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse; // Necesario para los métodos destroy

class AdminController extends Controller
{
    /**
     * Muestra una lista de TODOS los cursos creados por CUALQUIER vendedor.
     */
    public function indexCourses(): View
    {
        // Carga todos los cursos, incluyendo el usuario (vendedor) que lo creó.
        $courses = Course::with('user')->orderBy('created_at', 'desc')->paginate(15);
        
        return view('admin.global.courses', compact('courses'));
    }

    /**
     * Muestra una lista de TODAS las inscripciones realizadas por CUALQUIER comprador.
     */
    public function indexEnrollments(): View
    {
        // Carga todas las inscripciones, incluyendo el curso y el usuario (comprador).
        $enrollments = Enrollment::with(['course.user', 'user'])->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.global.enrollments', compact('enrollments'));
    }

    // =======================================================
    // NUEVOS MÉTODOS DE BORRADO GLOBAL
    // =======================================================
    
    /**
     * Elimina un curso del sistema de forma global.
     */
    public function destroyCourse(Course $course): RedirectResponse
    {
        // La ruta ya está protegida por el Gate 'manage-system'
        $course->delete();

        return back()->with('success', "Curso '{$course->title}' eliminado permanentemente.");
    }

    /**
     * Elimina un registro de inscripción del sistema.
     */
    public function destroyEnrollment(Enrollment $enrollment): RedirectResponse
    {
        $enrollment->delete();

        return back()->with('success', "Inscripción ID {$enrollment->id} eliminada.");
    }
}