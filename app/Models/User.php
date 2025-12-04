<?php

namespace App\Models;

// Importaciones necesarias
use App\Models\Course;
use App\Models\Enrollment; // Nueva importación
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
        'role', // Ya añadido
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

    // =======================================================
    // NUEVOS HELPERS PARA ROLES JERÁRQUICOS
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