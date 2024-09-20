<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tran_code',
        'type',
        'action',
        'first_balance',
        'before_balance',
        'after_balance',
        'note',
        'ip',
        'domain',
    ];

    protected $hidden = [
        'ip',
        'domain',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
