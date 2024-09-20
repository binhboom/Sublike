<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmmPanelPartner extends Model
{
    use HasFactory;

    protected $table = 'smm_panel_partners';

    protected $fillable = [
        'name',
        'url_api',
        'api_token',
        'price_update',
        'status',
        'update_price',
        'domain',
    ];
}
