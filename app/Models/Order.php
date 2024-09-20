<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_id',
        'server_id',
        'orderProviderName',
        'orderProviderServer',
        'order_package',
        'object_server',
        'object_id',
        'order_id',
        'order_code',
        'order_data',
        'start',
        'buff',
        'duration',
        'remaining', // số lượng, hoặc hết hạn
        'posts',
        'price',
        'payment',
        'status',
        'ip',
        'note',
        'error',
        'time',
        'domain',
        'created_at',
        'updated_at'
    ];

    protected $hidden =[
        'orderProviderName',
        'orderProviderServer',
        'order_data',
        'domain'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // đếm số ngày còn lại trong remaining
    public function remainingDays()
    {
        $remaining = $this->remaining;
        if ($remaining === 'Hết hạn') {
            return 'Hết hạn';
        }
        $start = strtotime($this->start);
        $duration = $this->duration;
        $end = strtotime("+$duration days", $start);
        $now = time();
        $remaining = $end - $now;
        if ($remaining < 0) {
            return 'Hết hạn';
        }
        return round($remaining / (60 * 60 * 24));
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function server()
    {
        return $this->belongsTo(ServiceServer::class, 'server_id');
    }

    public function orderdata()
    {
        return json_decode($this->order_data, true);
    }

    // public function getCreatedAtAttribute($value)
    // {
    //     return Carbon::parse($value)->format('d/m/Y H:i');
    // }

    // public function getUpdatedAtAttribute($value)
    // {
    //     return Carbon::parse($value)->diffForHumans();
    // }
}
