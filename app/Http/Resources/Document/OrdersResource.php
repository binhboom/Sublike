<?php

namespace App\Http\Resources\Document;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrdersResource extends JsonResource
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
            'user_id' => $this->user_id,
            'service_id' => $this->service_id,
            'server_id' => $this->server_id,
            'orderProviderName' => $this->orderProviderName,
            'orderProviderServer' => $this->orderProviderServer,
            'order_package' => $this->order_package,
            'object_server' => $this->object_server,
            'object_id' => $this->object_id,
            'order_id' => $this->order_id,
            'order_code' => $this->order_code,
            'start' => $this->start,
            'buff' => $this->buff,
            'price' => $this->price,
            'payment' => $this->payment,
            'status' => $this->status,
            'note' => $this->note,
            'service' => new ServiceByIdResource($this->service),
            'server' => new ServersResource($this->server),
        ];
    }
}
