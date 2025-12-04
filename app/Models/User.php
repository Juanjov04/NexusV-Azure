<?php

namespace App\Models;

// Importaciones necesarias
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\PaymentMethod; // <-- NUEVA IMPORTACIÓN para métodos de pago
use App\Models\Payment; // <-- IMPORTACIÓN NECESARIA para el historial de pagos

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- RELACIONES Y HELPERS ---

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }
    
    /**
     * Get the payment methods for the user.
     */
    public function paymentMethods(): HasMany // Nueva relación: métodos de pago guardados
    {
        return $this->hasMany(PaymentMethod::class);
    }
    
    /**
     * Get the payments made by the user (needed for history).
     */
    public function payments(): HasMany // Nueva relación: historial de pagos
    {
        return $this->hasMany(Payment::class);
    }


    // =======================================================
    // HELPERS PARA ROLES JERÁRQUICOS
    // =======================================================

    /**
     * Verifica si el usuario es el Administrador Maestro (Control Total).
     */
    public function isMasterAdmin(): bool
    {
        return $this->role === 'admin-master';
    }

    /**
     * Verifica si el usuario es Administrador (Maestro o Secundario).
     */
    public function isAdmin(): bool
    {
        // El rol 'admin-secondary' también cuenta como administrador.
        return in_array($this->role, ['admin-master', 'admin-secondary']);
    }
    
    /**
     * Verifica si el usuario tiene el rol de Vendedor.
     */
    public function isSeller(): bool
    {
        return $this->role === 'seller';
    }

    /**
     * Verifica si el usuario tiene el rol de Comprador.
     */
    public function isBuyer(): bool
    {
        return $this->role === 'buyer';
    }
}