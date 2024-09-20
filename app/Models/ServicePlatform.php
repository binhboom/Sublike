<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicePlatform extends Model
{
    use HasFactory;

    protected $table = 'service_platforms';

    protected $fillable = [
        'name',
        'code',
        'title',
        'description',
        'note',
        'details',
        'slug',
        'image',
        'status',
        'domain',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'domain'
    ];

    public function services()
    {
        return $this->hasMany(Service::class, 'platform_id', 'id');
    }
}
