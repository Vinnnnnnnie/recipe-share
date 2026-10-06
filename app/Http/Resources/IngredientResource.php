<?php

namespace App\Http\Resources;

use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Ingredient
 */
class IngredientResource extends JsonResource {

	/**
	 * Transform the resource into an array.
	 *
	 * @return array{
	 *     id:int,
	 *     name:string,
	 *     quantity:int,
	 *     measurement:string,
	 *     order:int
	 * }
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
