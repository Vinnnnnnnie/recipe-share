<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IngredientResource extends JsonResource {
    private readonly int $id;
    private string $name;
    private object $pivot;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'quantity' => $this->pivot?->quantity,
            'measurement' => $this->pivot?->measurement,
            'order' => $this->pivot?->order,
        ];
    }
}
