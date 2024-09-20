<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'level',
        'balance',
        'total_recharge',
        'status',
        'facebook',
        'telegram_link',
        'telegram_id',
        'notification_email',
        'notification_telegram',
        'two_factor_auth',
        'two_factor_secret',
        'avatar',
        'api_token',
        'access_login',
        'last_login',
        'last_ip',
        'login_expired_at',
        'domain',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'username';
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getLastLoginAttribute($value)
    {
        return Carbon::parse($value)->diffForHumans();
    }

    public function getAvatarAttribute($value)
    {
        //https://ui-avatars.com/api/?background=random&name=4b5d1503a946e080cd8b7f5252d6771f8fe410ab
        return $value ? $value : 'https://ui-avatars.com/api/?background=random&name=' . $this->username;
    }
}
