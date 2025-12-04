<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Seller\CourseController as SellerCourseController; // Controlador del Vendedor
use App\Http\Controllers\CourseController as PublicCourseController; // Controlador Público (Compradores)
use App\Http\Controllers\EnrollmentController; // Controlador de Inscripciones
use App\Http\Controllers\Admin\UserController; // Importación para gestión de usuarios Admin
use App\Http\Controllers\Admin\AdminController; // Importación para gestión global de cursos/inscripciones
use App\Http\Controllers\Seller\ModuleController; // NUEVA IMPORTACIÓN (Fase 2)
use App\Http\Controllers\CourseProgressController; // NUEVA IMPORTACIÓN (Fase 2)
use Illuminate\Support\Facades\Route;
use App\Models\Enrollment;
// RUTA PÚBLICA PARA LA VERIFICACIÓN DE CERTIFICADOS
use App\Http\Controllers\CertificateController;

Route::get('/certificate/verify/{uuid}', [CertificateController::class, 'verify'])
    ->name('certificate.verify');





// --------------------------------------------------------------------------------------
// 1. RUTAS PÚBLICAS Y REDIRECCIÓN DE INICIO (/)
// --------------------------------------------------------------------------------------

// Redirección de la ruta principal:
Route::get('/', function () {
    // Si el usuario está autenticado, enviarlo al dashboard (que redirigirá por rol).
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    // Si no está autenticado, lo enviamos a la vista pública de cursos.
    return redirect()->route('courses.index'); 
});

// Rutas Públicas de Cursos (Listado y Detalle)
Route::get('/courses', [PublicCourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course}', [PublicCourseController::class, 'show'])->name('courses.show');


// --------------------------------------------------------------------------------------
// 2. GRUPO DE RUTAS PROTEGIDAS POR AUTENTICACIÓN
// --------------------------------------------------------------------------------------
Route::middleware('auth')->group(function () {

    // RUTA DE CERTIFICADO: USAR EL ALIAS DE LA CLASE IMPORTADA    
    Route::get('courses/{course}/certify', [CourseProgressController::class, 'certify'])
        ->name('courses.certify');

    // RUTA DE CONTENIDO DEL CURSO: USAR EL ALIAS DE LA CLASE IMPORTADA    
    Route::get('/courses/{course}/content', [PublicCourseController::class, 'content'])
        ->name('courses.content');
    // Rutas de Perfil, Inscripción... (CÓDIGO EXISTENTE)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Rutas de Inscripción (Enrollment)
    Route::post('/enroll/{course}', [EnrollmentController::class, 'store'])
        ->name('enroll.store');

    // =======================================================
    // NUEVAS RUTAS DE PROGRESO (Fase 2)
    // =======================================================
    
    // ENDPOINT PARA REGISTRAR PROGRESO (Comprador marca lección como vista)
    Route::post('progress/complete/{module}', [CourseProgressController::class, 'store'])
        ->name('progress.store');


    // =======================================================
    // 5. NUEVAS RUTAS DE ADMINISTRACIÓN (Control Total)
    // =======================================================
    
    Route::middleware('can:manage-system')->prefix('admin')->group(function () {
        
        // Gestión de Usuarios (CRUD)
        Route::resource('users', UserController::class)
            ->names('admin.users') 
            ->only(['index', 'create', 'edit', 'update', 'destroy']);
        
        // Ruta para crear un nuevo Administrador Secundario (POST)
        Route::post('users/create-admin', [UserController::class, 'storeAdmin'])
            ->name('admin.users.store-admin');

        // GESTIÓN GLOBAL DE CURSOS E INSCRIPCIONES (Rutas Globales)
        Route::get('courses', [AdminController::class, 'indexCourses'])->name('admin.courses.index');
        Route::delete('courses/{course}', [AdminController::class, 'destroyCourse'])->name('admin.courses.destroy');
        Route::get('enrollments', [AdminController::class, 'indexEnrollments'])->name('admin.enrollments.index');
        Route::delete('enrollments/{enrollment}', [AdminController::class, 'destroyEnrollment'])->name('admin.enrollments.destroy');
    });

    // 4. RUTAS PARA VENDEDORES (Gestión de Cursos)
    // *** MODIFICACIÓN A SINTAXIS ::CLASS USANDO EL ALIAS ***
    Route::resource('seller/courses', SellerCourseController::class)
        ->names('seller.courses')
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
        ->middleware('can:is-seller');
    // *******************************************************
    
    // =======================================================
    // RUTAS ANIDADAS DE MÓDULOS (Fase 2)
    // =======================================================
    
    // Rutas de Gestión de Módulos (VENDEDOR)
    Route::resource('seller/courses.modules', ModuleController::class)
        ->names('seller.modules')
        ->except(['index', 'show']) 
        ->middleware('can:is-seller');
    
    // Ruta Dashboard (con Lógica de Redirección por Rol)
    Route::get('/dashboard', function () {
        $user = auth()->user();

        // 1. Redirección del Administrador
        if ($user->isAdmin()) {
            return redirect()->route('admin.users.index'); // <-- Redirigir al panel de control de usuarios
        }

        // 2. Redirección del Vendedor
        if ($user->isSeller()) {
            return redirect('/seller/courses'); 
        }

        // 3. Lógica del Comprador (Dashboard)
        $enrollments = Enrollment::with('course.user') 
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('dashboard', compact('enrollments'));
    })->middleware(['verified'])->name('dashboard');

});
// --------------------------------------------------------------------------------------


// 3. RUTAS DE AUTENTICACIÓN DE BREEZE (Login, Register, etc.)
require __DIR__.'/auth.php';