<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Intervention\Image\Laravel\Facades\Image;

Route::inertia('/', 'Home')->name('home');

Route::inertia('/cv', 'Cv')->name('cv');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('guest')->controller(AuthController::class)->group(function (): void {
	Route::get('/register', 'showRegister')->name('show.register');
	Route::get('/login', 'showLogin')->name('show.login');
	Route::post('/register', 'register')->name('register');
	Route::post('/login', 'login')->name('login');
});

Route::get('/public/images/users/{filename}/{height?}/{width?}', function (string $filename, ?int $height = 300, ?int $width = 300) {
	$path = public_path('storage/users/' . $filename);

	if (!file_exists($path) || is_dir($filename)) {
		$path = public_path('storage/users/defaults/Aubergine.jpg');
	}
	$imageData = file_get_contents($path);

	$image = Image::decode($imageData)->cover($height, $width);
	return response()->image($image);
})->name('image.users');


Route::get('/public/images/recipes/{filename}/{height?}/{width?}', function (string $filename, ?int $height = 500, ?int $width = 500) {
	$path = public_path('storage/recipes/' . $filename);
	if (!file_exists($path) || is_dir($filename)) {
		$path = public_path('storage/recipes/defaults/Plate.jpg');
	}
	$imageData = file_get_contents($path);

	$image = Image::decode($imageData)->cover($height, $width);
	return response()->image($image);
})->name('image.recipes');

Route::get('/public/images/website/{filename}', function ($filename) {
	$path = public_path('storage/website/' . $filename);
	if (!file_exists($path) || is_dir($filename)) {
		abort(404);
	}
	return response()->file($path);
})->name('image.website');

// Recipe Routes

Route::middleware('auth')->controller(UserController::class)->group(function (): void {
	Route::get('/users/edit', 'edit')->name('users.edit');
	Route::get('/users/savedRecipes', 'savedRecipes')->name('users.savedRecipes');
	Route::get('/users/settings', 'settings')->name('users.settings');
	Route::post('/users/follow', 'follow')->name('users.follow');
	Route::delete('/users/unfollow', 'unfollow')->name('users.unfollow');
	Route::post('/users/update', 'update')->name('users.update');
	Route::post('/users/saveRecipe/{recipe}', 'addSavedRecipe')->name('users.addSavedRecipe');
	Route::delete('/users/removeRecipe/{recipe}', 'removeSavedRecipe')->name('users.removeSavedRecipe');
});
Route::controller(UserController::class)->group(function (): void {
	Route::get('/users/{user}', 'show')->name('users.show');
});

Route::middleware('auth')->controller(RecipeController::class)->group(function (): void {
	Route::post('/recipes', 'store')->name('recipes.store');
	Route::get('/recipes/create', 'create')->name('recipes.create');
	Route::get('/recipes/scheduler', 'scheduler')->name('recipes.scheduler');
	Route::get('/recipes/searchByTerm/{term}', 'searchByTerm')->name('recipes.searchByTerm');
	Route::get('/recipes/edit/{recipe}', 'edit')->name('recipes.edit');
	Route::post('/recipes/update/{id}', 'update')->name('recipes.update');
	Route::delete('/recipes/{recipe}', 'destroy')->name('recipes.destroy');
});

Route::middleware('auth')->controller(IngredientController::class)->group(function (): void {
	Route::get('/ingredients/searchByTerm/{term}', 'searchByTerm')->name('ingredients.searchByTerm');
});

Route::controller(RecipeController::class)->group(function (): void {
	Route::get('/recipes', 'index')->name('recipes.index');
	Route::get('/recipes/search', 'search')->name('recipes.search');
	Route::get('/recipes/{recipe}', 'show')->name('recipes.show');
});


Route::post('/recipes/{recipe}', [CommentController::class, 'store'])->name('comments.store');
Route::delete('/comment/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');


Route::get('/games', fn() => Inertia::render('ComingSoon'))->name('games.index');

Route::get('/games/*', fn() => Inertia::render('ComingSoon'));
