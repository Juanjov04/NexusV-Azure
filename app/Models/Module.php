<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // Para vincular el progreso

class Module extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

   protected $fillable = [
        'course_id', 
        'title', 
        'content_url', 
        'sequence_order',
        'content_type', 
        'content_path'  
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    // Relación con el progreso (para verificar si un usuario ha completado el módulo)
    public function progress(): HasMany
    {
        return $this->hasMany(\App\Models\CourseProgress::class);
    }
}