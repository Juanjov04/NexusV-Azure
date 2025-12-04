<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;

class ModuleController extends Controller
{
    /**
     * Muestra el formulario para crear un nuevo módulo.
     */
    public function create(Course $course): View
    {
        if ($course->user_id !== Auth::id()) {
            abort(403, 'No autorizado para gestionar este curso.');
        }

        return view('courses.modules.create', compact('course'));
    }

    /**
     * Almacena un módulo recién creado.
     */
    public function store(Request $request, Course $course): RedirectResponse
    {
        // 1. Autorización
        if ($course->user_id !== Auth::id()) {
            abort(403);
        }

        // 2. Validación
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'sequence_order' => 'required|integer|min:1',
            'content_type' => 'required|in:link,video,document',
            'content_url' => 'nullable|url|required_if:content_type,link', 
            'media_file' => [
                'nullable', 
                'file', 
                'max:51200', // 50MB (50 * 1024)
                'mimes:mp4,pdf,doc,docx,mov,webm',
                'required_unless:content_type,link' 
            ],
        ]);

        $contentPath = null;
        $contentUrl = null;

        // 3. Lógica de Manejo de Contenido
        if ($data['content_type'] !== 'link') {
            // Es Video o Documento
            if ($request->hasFile('media_file')) { 
                $contentPath = $request->file('media_file')->store(
                    'modules/course_' . $course->id, 
                    'public' 
                );
            }
            // En este caso $contentUrl se queda como NULL, lo cual ahora es permitido por la BD.
        } else {
            // Es Link
            $contentUrl = $data['content_url'];
            // $contentPath se queda como NULL
        }
        
        // 4. Creación del Módulo
        $course->modules()->create([
            'title' => $data['title'],
            'sequence_order' => $data['sequence_order'],
            'content_type' => $data['content_type'],
            'content_url' => $contentUrl,
            'content_path' => $contentPath,
        ]);

        return redirect()->route('seller.courses.edit', $course)
                         ->with('success', 'Módulo creado exitosamente.');
    }
    
    /**
     * Muestra el formulario para editar un módulo.
     */
    public function edit(Course $course, Module $module): View
    {
        if ($course->user_id !== Auth::id() || $module->course_id !== $course->id) {
            abort(403, 'No autorizado.');
        }

        return view('courses.modules.edit', compact('course', 'module'));
    }

    /**
     * Actualiza un módulo existente.
     */
    public function update(Request $request, Course $course, Module $module): RedirectResponse
    {
        if ($course->user_id !== Auth::id() || $module->course_id !== $course->id) {
            abort(403);
        }
        
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'sequence_order' => 'required|integer|min:1',
            'content_type' => 'required|in:link,video,document',
            'content_url' => 'nullable|url|required_if:content_type,link', 
            'media_file' => [
                'nullable', 
                'file', 
                'max:51200', 
                'mimes:mp4,pdf,doc,docx,mov,webm',
            ],
        ]);

        $contentPath = $module->content_path;
        $contentUrl = null; 

        if ($data['content_type'] !== 'link') {
            // Lógica para Video/Documento
            if ($request->hasFile('media_file')) {
                if ($module->content_path) {
                    Storage::disk('public')->delete($module->content_path);
                }
                $contentPath = $request->file('media_file')->store(
                    'modules/course_' . $course->id, 
                    'public' 
                );
            }
            
            // Validación extra: si cambió de Link a Video y no subió nada
            if (is_null($contentPath) && !$request->hasFile('media_file')) {
                 return back()->withInput()->withErrors(['media_file' => 'Debes subir un archivo si cambias el tipo de contenido.']);
            }

        } else {
            // Lógica para Link
            $contentUrl = $data['content_url'];
            
            if ($module->content_path) {
                Storage::disk('public')->delete($module->content_path);
            }
            $contentPath = null;
        }
        
        $module->update([
            'title' => $data['title'],
            'sequence_order' => $data['sequence_order'],
            'content_type' => $data['content_type'],
            'content_url' => $contentUrl,
            'content_path' => $contentPath, 
        ]);

        return redirect()->route('seller.courses.edit', $course)
                         ->with('success', 'Módulo actualizado exitosamente.');
    }

    /**
     * Elimina un módulo.
     */
    public function destroy(Course $course, Module $module): RedirectResponse
    {
        if ($course->user_id !== Auth::id() || $module->course_id !== $course->id) {
            abort(403);
        }

        if ($module->content_path) {
            Storage::disk('public')->delete($module->content_path);
        }

        $module->delete();

        return redirect()->route('seller.courses.edit', $course)->with('success', 'Módulo eliminado.');
    }
}