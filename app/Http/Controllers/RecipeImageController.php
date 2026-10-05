<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\RecipeController;
use Illuminate\Support\Facades\Storage;

class RecipeImageController extends Controller {
    public function store(Request $request): string {
        $path = $request->file('image')->store('recipes');
        return $path;
    }
}
