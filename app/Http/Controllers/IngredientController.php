<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Measurement;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller {
    public static function getMeasurements() {
        return response()->json(Measurement::cases());
    }
    //
}
