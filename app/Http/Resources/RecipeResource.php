<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'title' => $this->title,
            'preparation_time' => $this->preparation_time,
            'cooking_time' => $this->cooking_time,
            'servings' => $this->servings,
            'difficulty' => $this->difficulty,
            'image_path' => $this->image_path,
            'user_id' => $this->user_id,
            'user' => $this->user,
            'ingredients' => IngredientResource::collection($this->ingredients),
            'steps' => $this->steps,
            'tags' => $this->tags,


        ];
    }
}
