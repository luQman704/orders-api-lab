<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'status' => $this->status,
            'placed_at' => $this->placed_at->toIso8601String(),
            'customer' => [
                'name' => $this->customer->name,
                'city' => $this->customer->city,
            ],
            'item_count' => $this->items->count(),
            'total' => number_format(
                $this->items->sum(fn ($item) => $item->quantity * $item->unit_price_cents) / 100, 2
            ),
        ];
    }
}
