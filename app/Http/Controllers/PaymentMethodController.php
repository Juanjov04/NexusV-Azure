<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class PaymentMethodController extends Controller
{
    /**
     * Procesa la solicitud para guardar un nuevo método de pago.
     */
    public function store(Request $request)
    {
        // 1. Validar los datos de la tarjeta simulada
        $validated = $request->validate([
            'card_holder_name' => 'required|string|max:255',
            'card_number' => 'required|numeric|digits_between:16,16',
            'expiry_month' => 'required|numeric|min:1|max:12',
            'expiry_year' => 'required|numeric|min:' . date('Y'), // Año actual o posterior
            'cvc' => 'required|numeric|digits:3',
            'is_default' => 'nullable|boolean',
        ], [
            'card_number.digits_between' => 'El número de tarjeta debe tener 16 dígitos.',
            'expiry_year.min' => 'El año de expiración debe ser el actual o posterior.',
        ]);

        $user = auth()->user();
        
        // --- Lógica de Simulación ---
        $lastFour = substr($validated['card_number'], -4);
        $brand = $this->getCardBrand($validated['card_number']);

        // Crear la fecha de expiración para la base de datos (se guarda como el último día del mes)
        $expiresAt = now()->setDate($validated['expiry_year'], $validated['expiry_month'], 1)->endOfMonth();

        // 2. Si se marca como default, desmarcar todas las demás
        if ($request->input('is_default')) {
            $user->paymentMethods()->update(['is_default' => false]);
        }

        // 3. Crear el nuevo PaymentMethod
        try {
            PaymentMethod::create([
                'user_id' => $user->id,
                'card_token' => $this->generateSimulatedToken($validated['card_number']), // Simulación de token único
                'brand' => $brand,
                'last_four' => $lastFour,
                'expires_at' => $expiresAt,
                'card_holder_name' => $validated['card_holder_name'],
                'is_default' => $request->boolean('is_default', true), // Por defecto es la predeterminada si es la primera
            ]);
        } catch (\Throwable $th) {
             // Manejar si el token ya existe (aunque es poco probable con la simulación)
             throw ValidationException::withMessages([
                'card_number' => ['Ocurrió un error al guardar la tarjeta. Intenta con otra o contacta soporte.'],
            ]);
        }


        return Redirect::route('profile.edit')->with('status', 'Método de pago agregado exitosamente.');
    }

    /**
     * Marca un método de pago existente como predeterminado.
     */
    public function setDefault(PaymentMethod $paymentMethod)
    {
        $this->authorize('update', $paymentMethod); // Asumiendo que tienes un Policy o Gate

        $user = auth()->user();

        // Desmarcar todos los demás
        $user->paymentMethods()->update(['is_default' => false]);

        // Marcar el seleccionado como predeterminado
        $paymentMethod->update(['is_default' => true]);

        return Redirect::route('profile.edit')->with('status', 'Método de pago predeterminado actualizado.');
    }

    /**
     * Elimina un método de pago.
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        $this->authorize('delete', $paymentMethod); // Asumiendo que tienes un Policy o Gate

        $paymentMethod->delete();

        return Redirect::route('profile.edit')->with('status', 'Método de pago eliminado exitosamente.');
    }
    
    // --- HELPERS DE SIMULACIÓN ---
    
    /**
     * Genera un token simulado único.
     */
    private function generateSimulatedToken($cardNumber): string
    {
        // En una pasarela real, esto sería el token devuelto. Aquí lo simulamos con un hash.
        return hash('sha256', $cardNumber . auth()->id() . time());
    }

    /**
     * Determina la marca de la tarjeta de forma simulada (solo para fines de UI).
     */
    private function getCardBrand($cardNumber): string
    {
        // Simulación muy simple basada en el primer dígito
        $firstDigit = substr($cardNumber, 0, 1);
        
        return match ($firstDigit) {
            '4' => 'Visa',
            '5' => 'Mastercard',
            '3' => 'Amex',
            default => 'Unknown',
        };
    }
}