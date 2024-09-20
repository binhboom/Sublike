<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServerAction extends Model
{
    use HasFactory;

    protected $table = 'server_actions';

    protected $fillable = [
        'server_id',
        'get_uid',
        'quantity_status',
        'reaction_status',
        'reaction_data',
        'comments_status',
        'comments_data',
        'minutes_status',
        'minutes_data',
        'posts_status',
        'posts_data',
        'time_status',
        'time_data',
        'duration_status',
        'duration_data',
        'refund_status',
        'warranty_status',
        'renews_status',
        'renews_type',
        'domain',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'domain',
    ];

    public function server()
    {
        return $this->belongsTo(ServiceServer::class, 'server_id', 'id');
    }
}
