<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RechargePromotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'min_balance',
        'percentage',
        'status',
        'domain',
    ];
}
