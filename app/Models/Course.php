<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Module;
use App\Models\Payment; // Importado para la nueva relación de pagos

class Course extends Model
{
    use HasFactory;

    public function modules(): HasMany
    {
        // Cargar módulos ordenados por el campo sequence_order
        return $this->hasMany(Module::class)->orderBy('sequence_order'); 
    }

    protected $fillable = [
        'user_id',
        'title',
        'header',
        'description',
        'scheduled_date',
        'price',
        'is_published',
    ];
    
    protected $casts = [
        'scheduled_date' => 'datetime', // Asegura que el campo se maneje como objeto fecha/hora
    ];

    /**
     * Get the user (seller) that owns the course.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    // --- RELACIONES EXISTENTES (Enrollment) ---

    /**
     * Get the users enrolled in this course.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Helper para verificar si un usuario ya está inscrito.
     */
    public function isEnrolled($userId): bool
    {
        return $this->enrollments()->where('user_id', $userId)->exists();
    }
    
    // --- NUEVA RELACIÓN PARA PAGOS ---

    /**
     * Get all successful payments for this course.
     */
    public function successfulPayments(): HasMany
    {
        return $this->hasMany(Payment::class)
                    ->where('status', 'succeeded');
    }
}