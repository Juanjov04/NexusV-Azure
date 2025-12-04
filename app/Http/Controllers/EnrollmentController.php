<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
   

    /**
     * Almacena una nueva inscripción para el usuario autenticado.
     */
    public function store(Course $course)
    {
        $user = Auth::user();

        // 1. Verificar si es un comprador
        if (!$user->isBuyer()) {
            return back()->with('error', 'Solo los compradores pueden inscribirse en cursos.');
        }

        // 2. Verificar si ya está inscrito
        if ($course->isEnrolled($user->id)) {
            return back()->with('info', 'Ya estás inscrito en este curso.');
        }

        // 3. Crear la inscripción
        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'paid', // Asumimos pago exitoso para esta fase
        ]);

        return redirect()->route('courses.show', $course)
            ->with('success', '¡Inscripción exitosa! Ahora puedes acceder al curso.');
    }
}