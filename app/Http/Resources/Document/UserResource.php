<?php

namespace App\Http\Resources\Document;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
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
            'avatar' => $this->avatar,
            'api_token' => $this->api_token,
            'last_login' => $this->last_login,
            'last_ip' => $this->last_ip,
            'order_data' => [
                'total_order' => $this->orders->count(),
            ]
        ];
    }
}
