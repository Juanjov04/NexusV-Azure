<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'card_token',
        'brand',
        'last_four',
        'expires_at',
        'card_holder_name',
        'is_default',
    ];

    protected $casts = [
        'expires_at' => 'date',
        'is_default' => 'boolean',
    ];

    /**
     * Get the user that owns the payment method.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    // Helper para formatear la expiración como MM/YY
    public function getExpirationDateAttribute()
    {
        return $this->expires_at->format('m/y');
    }
}