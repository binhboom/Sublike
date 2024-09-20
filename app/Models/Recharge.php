<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_code',
        'type',
        'payment_method',
        'bank_name',
        'bank_code',
        'amount',
        'real_amount',
        'status',
        'note',
        'is_send_telegram',
        'is_read',
        'paid_at',
        'expired_at',
        'domain'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bankcode()
    {
        return base64_decode($this->bank_code);
    }
}
