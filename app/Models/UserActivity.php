<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'user_id',
        'ip',
        'user_agent',
        'activity',
        'note',
        'domain',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'domain'
    ];

    /**
     * Get the user that owns the activity.
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user's last activity.
     */
}
