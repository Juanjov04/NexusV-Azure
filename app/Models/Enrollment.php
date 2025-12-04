<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'status', 
        'certificate_uuid', // ¡Añadido!
        'completed_at',     // ¡Añadido!
    ];
    
    // Casts para manejar automáticamente las fechas
    protected $casts = [
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user (buyer) associated with the enrollment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course associated with the enrollment.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
    
    /**
     * Define si el certificado ya ha sido generado.
     */
    public function hasCertificate(): bool
    {
        return !is_null($this->certificate_uuid) && !is_null($this->completed_at);
    }
}