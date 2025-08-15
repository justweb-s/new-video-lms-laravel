<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'amount_total',
        'currency',
        'status',
        'customer_email',
        'customer_details',
        'custom_fields',
        'metadata',
    ];

    protected $casts = [
        'amount_total' => 'integer',
        'customer_details' => 'array',
        'custom_fields' => 'array',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
