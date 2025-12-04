<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\PaymentMethod; // Importamos el modelo
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Muestra la página de checkout.
     * Si existe un método de pago predeterminado, se usa para el "Quick Checkout".
     */
    public function checkout(Course $course)
    {
        $user = auth()->user();
        
        // 1. Verificar si ya está matriculado
        if ($user->enrollments()->where('course_id', $course->id)->exists()) {
            return redirect()->route('courses.show', $course)->with('info', 'Ya estás suscrito a este curso.');
        }

        // 2. Buscar el método de pago predeterminado del usuario
        $defaultPaymentMethod = $user->paymentMethods()->where('is_default', true)->first();

        // 3. Decidir qué vista mostrar
        if ($defaultPaymentMethod) {
            // Si hay tarjeta, vamos al Quick Checkout (que solo pide confirmación)
            return view('payment.quick-checkout', compact('course', 'defaultPaymentMethod'));
        } else {
            // Si no hay tarjeta, vamos al checkout normal para registrar una
            return view('payment.checkout', compact('course')); 
        }
    }


    /**
     * Simula el procesamiento del pago (80% de éxito).
     * Puede recibir:
     * 1. Datos de tarjeta NUEVA (desde checkout.blade.php)
     * 2. ID de PaymentMethod guardado (desde quick-checkout.blade.php)
     */
    public function processPayment(Request $request, Course $course)
    {
        $user = auth()->user();
        $isSuccessful = (rand(1, 100) <= 80); // Lógica de simulación 80% de éxito
        $transactionId = (string) Str::uuid();

        // -----------------------------------------------------------
        // 1. DETERMINAR LA FUENTE DE PAGO (Tarjeta Nueva o Guardada)
        // -----------------------------------------------------------
        $paymentDetails = [];
        $paymentMethodModel = null; // Para guardar si se usa uno existente
        $shouldSaveCard = $request->boolean('save_card', false);

        if ($request->has('payment_method_id')) {
            // A) Pago Rápido (Quick Checkout) usando una tarjeta guardada
            $paymentMethodModel = PaymentMethod::where('user_id', $user->id)
                                              ->where('id', $request->input('payment_method_id'))
                                              ->firstOrFail();

            $paymentDetails = [
                'last_four' => $paymentMethodModel->last_four,
                'card_holder' => $paymentMethodModel->card_holder_name,
                'method_id' => $paymentMethodModel->id,
            ];
            $shouldSaveCard = false; // Ya está guardada
            
        } else {
            // B) Pago Normal usando una tarjeta nueva (con o sin opción de guardar)
            $request->validate([
                'card_holder' => 'required|string|max:255',
                'card_number' => 'required|numeric|digits_between:16,16',
                'expiry_date' => 'required|string', // formato MM/YY
                'cvc' => 'required|numeric|digits:3',
            ]);

            $paymentDetails = [
                'last_four' => substr($request->input('card_number'), -4),
                'card_holder' => $request->input('card_holder'),
            ];

            // Si el pago es exitoso Y el usuario marcó "guardar tarjeta", la registramos
            if ($isSuccessful && $shouldSaveCard) {
                $this->saveNewPaymentMethod($request);
            }
        }
        
        // -----------------------------------------------------------
        // 2. REGISTRAR PAGO Y MATRÍCULA
        // -----------------------------------------------------------
        
        $paymentData = [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'transaction_id' => $transactionId,
            'amount' => $course->price,
            'status' => $isSuccessful ? 'succeeded' : 'failed',
            'payment_details' => $paymentDetails,
        ];

        try {
            DB::beginTransaction();

            // Crea el registro de pago simulado
            Payment::create($paymentData);

            if ($isSuccessful) {
                // Conceder acceso (Enrollment)
                Enrollment::firstOrCreate([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                ], [
                    'status' => 'completed', 
                ]);

                DB::commit();
                return redirect()->route('payment.success', $course)->with('transaction_id', $transactionId);
            } else {
                DB::commit();
                return redirect()->route('payment.failure', $course)->with('transaction_id', $transactionId);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            // Esto manejaría errores de la DB, no fallos de la simulación
            return redirect()->route('payment.failure', $course)->with('error', 'Fallo interno al registrar la matrícula. Intente de nuevo.')->with('transaction_id', $transactionId);
        }
    }

    // --- HELPERS ---
    
    /**
     * Guarda el nuevo método de pago si el pago fue exitoso y se marcó la opción.
     */
    private function saveNewPaymentMethod(Request $request): void
    {
        $user = auth()->user();
        
        // Extraer mes/año
        $expiry = explode('/', $request->input('expiry_date'));
        $month = (int)$expiry[0];
        // Asumiendo formato YY, lo convertimos a YYYY
        $year = (int)(substr(date('Y'), 0, 2) . $expiry[1]);
        
        // Lógica de token y brand (simplificada, reutilizando lógica de PaymentMethodController)
        $lastFour = substr($request->input('card_number'), -4);
        $brand = $this->getCardBrand($request->input('card_number'));
        $expiresAt = now()->setDate($year, $month, 1)->endOfMonth();

        // Si es la primera, se hace default
        $isDefault = $user->paymentMethods()->doesntExist();
        if ($isDefault) {
            $user->paymentMethods()->update(['is_default' => false]);
        }

        PaymentMethod::create([
            'user_id' => $user->id,
            'card_token' => hash('sha256', $request->input('card_number') . $user->id . time()),
            'brand' => $brand,
            'last_four' => $lastFour,
            'expires_at' => $expiresAt,
            'card_holder_name' => $request->input('card_holder'),
            'is_default' => $isDefault,
        ]);
    }
    
    private function getCardBrand($cardNumber): string
    {
        $firstDigit = substr($cardNumber, 0, 1);
        
        return match ($firstDigit) {
            '4' => 'Visa',
            '5' => 'Mastercard',
            '3' => 'Amex',
            default => 'Unknown',
        };
    }
    
    /**
     * Muestra la página de éxito.
     */
    public function success(Course $course)
    {
        $transactionId = session('transaction_id') ?? 'N/A';
        return redirect()->route('courses.show', $course)->with('success', 
            "¡Felicidades! La compra y matrícula en el curso '{$course->title}' fue exitosa. ID Transacción: {$transactionId}"
        );
    }

    /**
     * Muestra la página de fallo.
     */
    public function failure(Course $course)
    {
        $transactionId = session('transaction_id') ?? 'N/A';
        $error = session('error') ?? 'El pago fue rechazado por la pasarela simulada (20% de probabilidad).';

        return redirect()->route('courses.show', $course)->with('error', 
            "Error en la compra: {$error} ID Transacción: {$transactionId}"
        );
    }
}