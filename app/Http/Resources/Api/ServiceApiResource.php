<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceApiResource extends JsonResource
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
            "id" => $this->id,
            "code" => $this->code,
            "name" => $this->name,
            "title" => $this->title,
            "description" => $this->title,
            "note" => $this->note,
            "details" => $this->details,
            "package" => $this->package,
            "slug" => $this->slug,
            "image" => $this->image,
            "status" => $this->status,
            "platform_id" => $this->platform_id,
            "reaction_status" => $this->reaction_status,
            "quantity_status" => $this->quantity_status,
            "comments_status" => $this->comments_status,
            "minutes_status" => $this->minutes_status,
            "time_status" => $this->time_status,
            "posts_status" => $this->posts_status,
        ];
    }
}
