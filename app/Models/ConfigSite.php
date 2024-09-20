<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigSite extends Model
{
    use HasFactory;

    protected $table = 'config_sites';

    protected $fillable = [
        'name_site',
        'title',
        'description',
        'keywords',
        'author',
        'thumbnail',
        'logo',
        'favicon',
        'facebook',
        'zalo',
        'telegram',
        'maintenance',
        'collaborator',
        'agency',
        'distributor',
        'percent_member',
        'percent_collaborator',
        'percent_agency',
        'percent_distributor',
        'start_promotion',
        'end_promotion',
        'percent_promotion',
        'transfer_code',
        'partner_id',
        'partner_key',
        'percent_card',
        'cloudflare_email',
        'cloudflare_api_key',
        'cloudflare_zone_id',
        'cloudflare_global_key',
        'nameserver_1',
        'nameserver_2',
        'telegram_bot_token',
        'telegram_chat_id',
        'telegram_bot_group',
        'telegram_bot_chat',
        'telegram_bot_chat_token',
        'telegram_bot_chat_username',
        'notice',
        'script_head',
        'script_body',
        'script_footer',
        'admin_username',
        'site_token',
        'status',
        'is_domain',
        'domain',
    ];

    public function userAdmin()
    {
        return $this->belongsTo(User::class, 'admin_username', 'username');
    }


}
