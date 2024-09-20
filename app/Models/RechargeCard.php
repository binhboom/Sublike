<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RechargeCard extends Model
{
    use HasFactory;

    protected $table = 'recharge_cards';

    protected $fillable = [
        'code',
        'user_id',
        'type',
        'amount',
        'real_amount',
        'serial',
        'pin',
        'status',
        'tran_id',
        'note',
        'domain',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
