<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\IngredientResource;
use App\Measurement;
use App\Models\Ingredient;
use Illuminate\Http\Resources\Json\JsonResource;

class IngredientController extends Controller {
	public static function getMeasurements() {
		return response()->json(Measurement::cases());
	}

	public function searchByTerm(string $term): JsonResource {
		$ingredients = Ingredient::searchByTerm($term);
		return IngredientResource::collection($ingredients);
	}
	//
}
