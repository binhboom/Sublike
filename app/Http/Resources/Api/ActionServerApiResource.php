<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActionServerApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'get_uid' => $this->get_uid,
            'quantity_status' => $this->quantity_status,
            'reaction_status' => $this->reaction_status,
            'reaction_data' => $this->reaction_data,
            'comments_status' => $this->comments_status,
            'comments_data' => $this->comments_data,
            'minutes_status' => $this->minutes_status,
            'minutes_data' => $this->minutes_data,
            'posts_status' => $this->posts_status,
            'posts_data' => $this->posts_data,
            'time_status' => $this->time_status,
            'time_data' => $this->time_data,
            'duration_status' => $this->duration_status,
            'duration_data' => $this->duration_data,
            'refund_status' => $this->refund_status,
            'warranty_status' => $this->warranty_status,
            'renews_status' => $this->renews_status,
            'renew_type' => $this->renew_type,
        ];
    }
}
