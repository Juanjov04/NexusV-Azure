<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        // Añadimos las relaciones necesarias para mostrar métodos de pago
        $user = $request->user()->load('paymentMethods'); 
        
        return view('profile.edit', [
            'user' => $user,
        ]);
    }
    
    /**
     * Muestra el historial de pagos del usuario comprador.
     */
    public function payments(Request $request): View
    {
        $user = $request->user();
        
        // Obtenemos los pagos exitosos y fallidos con los datos del curso
        $payments = $user->payments()
            ->with('course') // Asumiendo que has agregado la relación 'payments' al modelo User y 'course' al modelo Payment
            ->latest()
            ->get();
            
        return view('profile.payments', compact('payments'));
    }


    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}