<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\Document\ServicesWithServersResources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlatformServiceApiResource extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'slug' => $this->slug,
            'image' => $this->image,
            'services' => ServicesWithServersResources::collection($this->services->where('domain', env('APP_MAIN_SITE')))
        ];
    }
}
