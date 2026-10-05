<?php

declare(strict_types=1);

namespace App\Models;

use App\Measurement;
use Database\Factories\IngredientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class Ingredient extends Model {
    /** @use HasFactory<IngredientFactory> */
    use HasFactory;

    protected $fillable = ['name', 'recipe_id', 'number', 'measurement', 'quantity'];

    public function recipes(): BelongsToMany {
        return $this->belongsToMany(Recipe::class, 'recipe_ingredient', 'ingredient_id', 'recipe_id');
    }


    protected function casts(): array {
        return [
            'measurement' => Measurement::class,
        ];
    }

    public static function addIngredientsToRecipe(Recipe $recipe, Request $request): \Illuminate\Http\JsonResponse {

        // Validation
        $validated = $request->validate(
            [
                'ingredients' => 'required|array|min:1',
                'ingredients.*.name' => 'required|string|max:64',
                'ingredients.*.quantity' => 'required|numeric|min:0',
                'ingredients.*.measurement' => [Rule::enum(Measurement::class)],
            ],
            [
                'ingredients.required|min:1' => 'You must have 1 ingredient.',
                'ingredients.*.name' => 'Ingredients cannot be greater than 64 characters.',
            ]
        );


        $counter = 0;
        foreach ($validated['ingredients'] as $ingredient) {
            // Does ingredient already exist?
            if (Ingredient::where($ingredient['name'], 'name')->doesntExist()) {
                // If it doesn't, make it
                $ingredientModel = Ingredient::create(
                    [
                        'name' => $ingredient['name'],
                    ]
                );
            }
            // Find the ingredient
            $ingredientModel = Ingredient::where('name', '=', $ingredient['name'])->get()->first();

            // Attach the recipe to the pivot table and add the additional info
            $ingredientModel->recipes()->attach($recipe->id, [
                'quantity' => $ingredient['quantity'],
                'measurement' => $ingredient['measurement'],
                'order' => $counter
            ]);

            $counter++;
        }
        return response()->json(['status' => 'ok', 'msg' => 'Ingredients added successfully.']);
    }
}
