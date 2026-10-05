<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\StepFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class Step extends Model {
    /** @use HasFactory<StepFactory> */
    use HasFactory;

    protected $fillable = ['step', 'recipe_id', 'number'];

    public static function addStepsToRecipe(Recipe $recipe, Request $request): void {
        $validated = $request->validate(
            [
                'steps' => 'required|array|min:1',
                'steps.*.step' => 'required|string|max:255|min:3',
            ],
            [
                'steps.required|min:1' => 'You must have 1 step.',
                'steps.*.step' => 'Your steps must be between 3-255 characters.',
            ]
        );
        $counter = 0;

        foreach ($validated['steps'] as $step) {
            Step::create(
                [
                    'step' => $step['step'],
                    'number' => $counter,
                    'recipe_id' => $recipe->id,
                ]
            );
            $counter++;
        }
    }
}
