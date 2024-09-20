<?php

namespace App\Http\Resources\Document;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServersResource extends JsonResource
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
            'service_id' => $this->service_id,
            'name' => $this->name,
            'details' => $this->details,
            'package_id' => $this->package_id,
            'price_member' => $this->price_member,
            'price_collaborator' => $this->price_collaborator,
            'price_agency' => $this->price_agency,
            'price_distributor' => $this->price_distributor,
            'min' => $this->min,
            'max' => $this->max,
            'limit_day' => $this->limit_day,
            'status' => $this->status,
            'options' => new ServersActionResource($this->action),
        ];
    }
}
