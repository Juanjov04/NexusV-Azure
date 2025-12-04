<?php

namespace App\Http\Controllers\Seller; 

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse; 

class CourseController extends Controller
{
    /**
     * Muestra la lista de cursos creados por el vendedor autenticado.
     */
    public function index(): View
    {
        // Obtener SOLO los cursos asociados al usuario logueado, usando paginate para la vista.
        $courses = Auth::user()->courses()->latest()->paginate(10); 

        // Retorna la vista específica para la gestión del vendedor
        return view('courses.seller-index', compact('courses')); 
    }

    /**
     * Muestra el formulario para crear un nuevo curso.
     */
    public function create(): View
    {
        // Retorna la vista que contiene el formulario de creación.
        return view('courses.create'); 
    }
    
    /**
     * Almacena un curso recién creado en la base de datos.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validación de los datos
        $validatedData = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'header' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'scheduled_date' => ['required', 'date', 'after:tomorrow'], 
            'price' => ['required', 'numeric', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        // 2. Creación del curso
        Auth::user()->courses()->create([
            'title' => $validatedData['title'],
            'header' => $validatedData['header'],
            'description' => $validatedData['description'],
            'scheduled_date' => $validatedData['scheduled_date'],
            'price' => $validatedData['price'],
            'is_published' => $request->has('is_published'),
        ]);

        return redirect()->route('seller.courses.index')->with('success', '¡Curso agendado y guardado correctamente!');
    }

    /**
     * Muestra un curso específico del vendedor.
     */
    public function show(Course $course)
    {
        abort(404); // Mantenemos el abort 404 para evitar acceso directo
    }
    
    /**
     * Muestra el formulario para editar un curso existente. (CORRECCIÓN FINAL)
     */
    public function edit(Course $course): View
    {
        // === SOLUCIÓN FINAL: PERMISO PARA ADMIN MAESTRO O DUEÑO ===
        // Si es Master Admin, el acceso se permite inmediatamente.
        if (Auth::user()->isMasterAdmin() || $course->user_id === Auth::id()) {
            return view('courses.edit', compact('course'));
        }
        
        // Si no cumple ninguna de las dos condiciones, denegar el acceso.
        abort(403, 'Acción no autorizada. Solo el dueño del curso o un Administrador Maestro puede editarlo.');
    }

    /**
     * Actualiza un curso en la base de datos. (CORRECCIÓN FINAL)
     */
    public function update(Request $request, Course $course): RedirectResponse
    {
        // === SOLUCIÓN: PERMISO PARA ADMIN MAESTRO O DUEÑO ===
        // Si NO es Master Admin Y NO es el dueño, abortar.
        if (!Auth::user()->isMasterAdmin() && $course->user_id !== Auth::id()) {
            abort(403, 'Acción no autorizada.');
        }
        
        // Lógica de actualización (ejecutada si el permiso es concedido)
        $validatedData = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'header' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'scheduled_date' => ['required', 'date', 'after:tomorrow'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
        ]);
        
        $course->update([
            'title' => $validatedData['title'],
            'header' => $validatedData['header'],
            'description' => $validatedData['description'],
            'scheduled_date' => $validatedData['scheduled_date'],
            'price' => $validatedData['price'],
            'is_published' => $request->has('is_published'),
        ]);

        return redirect()->route('seller.courses.index')->with('success', '¡Curso actualizado correctamente!');
    }

    /**
     * Elimina un curso de la base de datos. (CORRECCIÓN FINAL)
     */
    public function destroy(Course $course): RedirectResponse
    {
        // === SOLUCIÓN: PERMISO PARA ADMIN MAESTRO O DUEÑO ===
        // Si NO es Master Admin Y NO es el dueño, abortar.
        if (!Auth::user()->isMasterAdmin() && $course->user_id !== Auth::id()) {
            abort(403, 'Acción no autorizada.');
        }
        
        $course->delete();

        return redirect()->route('seller.courses.index')->with('success', 'Curso eliminado correctamente.');
    }
}