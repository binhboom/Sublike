<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        /* {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "user_id": 1,
                "service_id": 1,
                "server_id": 4,
                "order_package": "like",
                "object_server": "sv-5",
                "object_id": "100068743724597",
                "order_id": "17176963417289",
                "order_code": "INV_249269413",
                "start": "0",
                "buff": "0",
                "duration": "30",
                "remaining": "30",
                "posts": "0",
                "price": "6",
                "payment": "600",
                "status": "Refunded",
                "ip": "127.0.0.1",
                "note": "",
                "error": null,
                "time": "2024-06-07 00:52:21",
                "created_at": "2024-06-06T17:52:21.000000Z",
                "updated_at": "2024-06-06T18:07:16.000000Z"
            },
            {
                "id": 2,
                "user_id": 1,
                "service_id": 1,
                "server_id": 4,
                "order_package": "like",
                "object_server": "5",
                "object_id": "123123",
                "order_id": "17177919144510",
                "order_code": "INV_242975609",
                "start": "0",
                "buff": "0",
                "duration": "7",
                "remaining": "7",
                "posts": "0",
                "price": "6",
                "payment": "6000",
                "status": "Processing",
                "ip": "127.0.0.1",
                "note": null,
                "error": null,
                "time": "2024-06-08 03:25:14",
                "created_at": "2024-06-07T20:25:14.000000Z",
                "updated_at": "2024-06-07T20:25:14.000000Z"
            },
            {
                "id": 3,
                "user_id": 1,
                "service_id": 1,
                "server_id": 4,
                "order_package": "like",
                "object_server": "5",
                "object_id": "ádklfjsadf",
                "order_id": "17177919929817",
                "order_code": "INV_243081441",
                "start": "0",
                "buff": "0",
                "duration": "7",
                "remaining": "7",
                "posts": "0",
                "price": "6",
                "payment": "6000",
                "status": "Processing",
                "ip": "127.0.0.1",
                "note": null,
                "error": null,
                "time": "2024-06-08 03:26:32",
                "created_at": "2024-06-07T20:26:32.000000Z",
                "updated_at": "2024-06-07T20:26:32.000000Z"
            }
        ],
        "first_page_url": "http://127.0.0.1:8000/api/v1/account/orders?page=1",
        "from": 1,
        "last_page": 1,
        "last_page_url": "http://127.0.0.1:8000/api/v1/account/orders?page=1",
        "links": [
            {
                "url": null,
                "label": "&laquo; Trang sau",
                "active": false
            },
            {
                "url": "http://127.0.0.1:8000/api/v1/account/orders?page=1",
                "label": "1",
                "active": true
            },
            {
                "url": null,
                "label": "Trang trước &raquo;",
                "active": false
            }
        ],
        "next_page_url": null,
        "path": "http://127.0.0.1:8000/api/v1/account/orders",
        "per_page": 10,
        "prev_page_url": null,
        "to": 3,
        "total": 3
    } */

        return [
            // add pagination
            'current_page' => $this->currentPage(),
            'data' => $this->collection,
            'first_page_url' => $this->url(1),
            'from' => $this->firstItem(),
            'last_page' => $this->lastPage(),
            'last_page_url' => $this->url($this->lastPage()),
            'links' => $this->links(),
            'next_page_url' => $this->nextPageUrl(),
            'path' => $this->path(),
            'per_page' => $this->perPage(),
            'prev_page_url' => $this->previousPageUrl(),
            'to' => $this->lastItem(),
            'total' => $this->total(),
        ];
    }
}
