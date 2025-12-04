<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportsController extends Controller
{
    public function __construct()
    {
        // Aplicar el middleware de vendedor
        $this->middleware('can:is-seller');
    }
    
    /**
     * Muestra el reporte de ventas del vendedor, detallado por curso.
     */
    public function index(): View
    {
        $user = auth()->user();
        
        // 1. Obtener los cursos del vendedor con sus pagos exitosos cargados
        $courses = $user->courses()
                        ->with('successfulPayments')
                        ->get();
                        
        // 2. Procesar los datos para el reporte
        $reports = $courses->map(function ($course) {
            
            // Contar pagos exitosos (matriculados)
            $enrollmentCount = $course->successfulPayments->count();
            
            // Calcular el total de ingresos
            $totalRevenue = $course->successfulPayments->sum('amount');
            
            return [
                'course_id' => $course->id,
                'title' => $course->title,
                'price' => $course->price,
                'status' => $course->is_published ? 'Publicado' : 'Borrador',
                'enrollment_count' => $enrollmentCount,
                'total_revenue' => $totalRevenue,
            ];
        })->sortByDesc('total_revenue'); // Ordenar por ingresos

        // 3. Totales globales
        $globalTotals = [
            'total_courses' => $reports->count(),
            'total_enrollments' => $reports->sum('enrollment_count'),
            'total_revenue' => $reports->sum('total_revenue'),
        ];
        
        return view('seller.reports', compact('reports', 'globalTotals'));
    }
}