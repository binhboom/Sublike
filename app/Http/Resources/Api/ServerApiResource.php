<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServerApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        // nếu có $request->user
        if ($request->user) {
            $price_member = $this->price_member;
            $price_collaborator = $this->price_collaborator;
            $price_agency = $this->price_agency;

            $price = 0;
            if ($request->user->level == 'member') {
                $price = $price_member;
            } elseif ($request->user->level == 'collaborator') {
                $price = $price_collaborator;
            } elseif ($request->user->level == 'agency') {
                $price = $price_agency;
            }
        } else {
            $price = $this->price_member;
        }

        return [
            'id' => $this->id,
            'service_id' => $this->service_id,
            'name' => $this->name,
            'details' => $this->details,
            'package_id' => $this->package_id,
            'price' => $price,
            'price_format' => $price . 'đ',
            'min' => $this->min,
            'max' => $this->max,
            'limit_day' => $this->limit_day,
            'status' => $this->status,
            'action' => new ActionServerApiResource($this->actions->first()),
        ];
    }
}
