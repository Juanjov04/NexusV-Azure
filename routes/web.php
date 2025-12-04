<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Seller\CourseController as SellerCourseController; // Controlador del Vendedor
use App\Http\Controllers\CourseController as PublicCourseController; // Controlador Público (Compradores)
use App\Http\Controllers\EnrollmentController; // Controlador de Inscripciones
use App\Http\Controllers\Admin\UserController; // Importación para gestión de usuarios Admin
use App\Http\Controllers\Admin\AdminController; // Importación para gestión global de cursos/inscripciones
use App\Http\Controllers\Seller\ModuleController; // NUEVA IMPORTACIÓN (Fase 2)
use App\Http\Controllers\CourseProgressController; // NUEVA IMPORTACIÓN (Fase 2)

// =======================================================
// NUEVAS IMPORTACIONES DE PAGO Y REPORTES
// =======================================================
use App\Http\Controllers\PaymentController; // IMPORTACIÓN CLAVE
use App\Http\Controllers\PaymentMethodController; // <-- NUEVA IMPORTACIÓN
use App\Http\Controllers\Seller\ReportsController; // <-- NUEVA IMPORTACIÓN
use App\Http\Controllers\CertificateController; // RUTA PÚBLICA PARA LA VERIFICACIÓN DE CERTIFICADOS

use Illuminate\Support\Facades\Route;
use App\Models\Enrollment;


// RUTA PÚBLICA PARA LA VERIFICACIÓN DE CERTIFICADOS
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

    // =======================================================
    // 💳 RUTAS DE PAGO SIMULADO (PaymentController)
    // =======================================================

    // 1. Mostrar el formulario de checkout (adaptado para Quick Checkout o registro)
    Route::get('/courses/{course}/checkout', [PaymentController::class, 'checkout'])
        ->name('payment.checkout');

    // 2. Procesar el pago simulado (POST)
    Route::post('/payment/process/{course}', [PaymentController::class, 'processPayment'])
        ->name('payment.process');
    
    // 3. Redirección de éxito
    Route::get('/payment/success/{course}', [PaymentController::class, 'success'])
        ->name('payment.success');

    // 4. Redirección de fallo
    Route::get('/payment/failure/{course}', [PaymentController::class, 'failure'])
        ->name('payment.failure');


    // =======================================================
    // 💳 RUTAS DE GESTIÓN DE MÉTODOS DE PAGO (PaymentMethodController)
    // =======================================================
    Route::prefix('payment-methods')->group(function () {
        Route::post('/', [PaymentMethodController::class, 'store'])->name('payment-methods.store');
        Route::patch('/{paymentMethod}/default', [PaymentMethodController::class, 'setDefault'])->name('payment-methods.set-default');
        Route::delete('/{paymentMethod}', [PaymentMethodController::class, 'destroy'])->name('payment-methods.destroy');
    });

    // RUTA DE CERTIFICADO
    Route::get('courses/{course}/certify', [CourseProgressController::class, 'certify'])
        ->name('courses.certify');

    // RUTA DE CONTENIDO DEL CURSO
    Route::get('/courses/{course}/content', [PublicCourseController::class, 'content'])
        ->name('courses.content');
        
    // Rutas de Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/payments', [ProfileController::class, 'payments'])->name('profile.payments'); // <-- NUEVA RUTA: Historial de Pagos
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Rutas de Inscripción (Enrollment) - Mantenida por si acaso, aunque el pago la reemplaza.
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
    Route::resource('seller/courses', SellerCourseController::class)
        ->names('seller.courses')
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
        ->middleware('can:is-seller');
    
    // =======================================================
    // RUTAS DE REPORTES DEL VENDEDOR
    // =======================================================
    Route::get('seller/reports', [ReportsController::class, 'index'])
        ->name('seller.reports.index')
        ->middleware('can:is-seller');
    
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
            return redirect()->route('seller.courses.index'); // Redirigir al listado de cursos del vendedor
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