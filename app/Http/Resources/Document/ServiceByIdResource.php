<?php

namespace App\Http\Resources\Document;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceByIdResource extends JsonResource
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
            'order' => $this->order,
            'code' => $this->code,
            'name' => $this->name,
            'note' => $this->note,
            'details' => $this->details,
            'package' => $this->package,
            'slug' => $this->slug,
            'status' => $this->status,
            'category' => $this->platform->slug,
            'servers' => ServersResource::collection($this->servers),
        ];
    }
}
