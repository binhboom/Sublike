<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'order',
        'code',
        'name',
        'title',
        'description',
        'note',
        'details',
        'package',
        'slug',
        'image',
        'status',
        'platform_id',
        'reaction_status',
        'quantity_status',
        'comments_status',
        'minutes_status',
        'time_status',
        'posts_status',
        'domain',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'domain'
    ];

    public function platform()
    {
        return $this->belongsTo(ServicePlatform::class);
    }

    public function servers()
    {
        return $this->hasMany(ServiceServer::class);
    }

    public function server()
    {
        return $this->hasOne(ServiceServer::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
