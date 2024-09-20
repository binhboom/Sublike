<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'role' => $this->role,
            'level' => $this->level,
            'balance' => $this->balance,
            'total_recharge' => $this->total_recharge,
            'status' => $this->status,
            'telegram_id' => $this->telegram_id,
            'two_factor_auth' => $this->two_factor_auth,
            'two_factor_secret' => $this->two_factor_secret,
            'avatar' => $this->avatar,
            'api_token' => $this->api_token,
            'last_login' => $this->last_login,
            'last_ip' => $this->last_ip,
            'access_login' => $this->access_login,
            'login_expired_at' => $this->login_expired_at,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at
        ];
    }
}
