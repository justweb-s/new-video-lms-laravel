<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiftCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'course_id',
        'buyer_user_id',
        'recipient_name',
        'recipient_email',
        'message',
        'amount',
        'currency',
        'provider',
        'status',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'paypal_order_id',
        'paypal_capture_id',
        'issued_at',
        'redeemed_at',
        'redeemer_user_id',
    ];

    protected $casts = [
        'amount' => 'integer',
        'issued_at' => 'datetime',
        'redeemed_at' => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_user_id');
    }

    public function redeemer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'redeemer_user_id');
    }
}
