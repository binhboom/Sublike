<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banking extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_name',
        'account_name',
        'account_number',
        'logo',
        'bank_account',
        'bank_password',
        'min_recharge',
        'status',
        'token',
        'domain',
    ];

    protected $hidden = [
        'bank_password',
        'token',
    ];
}
