<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerWebsite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'url',
        'status',
        'zone_id',
        'zone_name',
        'zone_status',
        'zone_data',
        'is_domain',
        'domain',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
