<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Module;

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
        'scheduled_date' => 'datetime', //si no agregamos esta linea protected al guardar la fecha tentativa en un curso se daña
    ];

    /**
     * Get the user (seller) that owns the course.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    // --- NUEVAS RELACIONES Y HELPERS ---

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
}