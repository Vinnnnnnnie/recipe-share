<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\RecipeResource;
use App\Measurement;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Step;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RecipeController extends Controller {
	//
	public function index() {
		return Inertia::render(
			'Recipes/Index',
			[
				'recipes' => self::scrollableRecipeList(),
			]
		);
	}

	public function scrollableRecipeList() {
		return Inertia::scroll(
			fn() => Recipe::with(
				[
					'user',
					'comments' => function ($query): void {
						$query->select('id', 'user_id', 'recipe_id');
					},
					'savedUsers' => function ($query): void {
						$query->select('user_id', 'recipe_id');
					},
					'tags',
				]
			)
				->select(
					'id',
					'title',
					'user_id',
					'created_at',
					'preparation_time',
					'cooking_time',
					'servings',
					'difficulty',
					'image_path'
				)
				->orderBy('created_at', 'desc')
				->paginate()
		);
	}

	public function show(Recipe $recipe) {
		$recipe->load('user', 'comments.user', 'tags', 'ingredients', 'steps');
		$comments = $recipe->comments;
		$tags = $recipe->tags;
		$recipe = new RecipeResource($recipe);
		return Inertia::render(
			'Recipes/Show',
			[
				'recipe' => $recipe,
				'comments' => $comments,
				'tags' => $tags,
			]
		);
	}

	public function create() {
		$tags = Tag::orderBy('name')->get();
		return Inertia::render('Recipes/Create', ['tags' => $tags, 'measurements' => Measurement::cases()]);
	}

	/**
	 * @throws \Throwable
	 */
	public function store(Request $request) {
		// Image upload
		if (isset($request->image)) {
			$image_path = $request->image->store("recipes", 'public');
			$image_path = str_replace('recipes/', '', $image_path);
			$request->merge(['image_path' => $image_path]);
		}
		$request->merge(['user_id' => auth()->user()->id]);

		$validated = $request->validate([
			'title' => 'required|string|max:32',
			'preparation_time' => 'required|integer|min:1',
			'cooking_time' => 'required|integer|min:1',
			'servings' => 'required|integer|min:1',
			'difficulty' => 'required|string|in:Easy,Medium,Hard',
			'user_id' => 'required|integer|exists:users,id',
			'image_path' => 'string',
			'tags' => 'array',
		]);
		DB::transaction(function () use ($request, $validated): void {
			$recipe = Recipe::create($validated);
			Ingredient::addIngredientsToRecipe($recipe, $request);
			Step::addStepsToRecipe($recipe, $request);
			if ($request->tags !== null) {
				$recipe->tags()->sync($request->tags);
			}
			User::addReputation(auth()->user(), 1);
		});

		return redirect()->route('recipes.index')->with('success', 'Recipe added successfully!');
	}

	public function destroy(Recipe $recipe) {
		if ($recipe->user_id !== auth()->user()->id) {
			return back()->withErrors('Operation not permitted.');
		}
		User::subtractReputation(auth()->user(), 1);
		$recipe->delete();
		return redirect()->route('recipes.index')->with('success', 'Recipe deleted successfully!');
	}

	public function edit($id) {
		$recipe = Recipe::where('id', $id)->first()->load('user', 'comments.user', 'tags', 'ingredients', 'steps');

		$recipe = new RecipeResource($recipe);
		if ($recipe->user_id !== auth()->user()->id) {
			return redirect()->route('recipes.index')->withErrors('You are not the owner of this recipe.');
		}
		$tags = Tag::orderBy('name', 'asc')->get();
		return Inertia::render('Recipes/Edit', ['recipe' => $recipe, 'tags' => $tags]);
	}

	public function search(Request $request) {
		$term = $request->term;
		$validated = $request->validate(['term' => 'required|max:64']);
		$recipes = Inertia::scroll(fn() => Recipe::query()
			->select('users.image_path', 'recipes.image_path', 'recipes.*', 'users.username')
			->join('users', 'users.id', '=', 'recipes.user_id')
			->where('recipes.title', 'LIKE', '%' . $validated['term'] . '%')
			->orWhere('users.username', 'LIKE', $validated['term'] . '%')
			->with(
				[
					'user',
					'comments' => function ($query): void {
						$query->select('id', 'user_id', 'recipe_id');
					},
					'savedUsers' => function ($query): void {
						$query->select('user_id', 'recipe_id');
					},
					'tags']
			)
			->orderBy('recipes.created_at', 'DESC')
			->paginate(15));
		$users = User::query()
			->select('image_path', 'username', 'id', 'reputation')
			->where('username', 'LIKE', $term . '%')
			->limit(5)
			->get();
		return Inertia::render('Recipes/Search', ['recipes' => $recipes, 'users' => $users]);
	}

	public function update(Request $request) {
		if (isset($request->image)) {
			$image_path = $request->image->store("recipes", 'public');
			$image_path = str_replace('recipes/', '', $image_path);
			$request->merge(['image_path' => $image_path]);
		}

		$validated = $request->validate([
			'id' => 'required|integer|exists:recipes',
			'title' => 'required|string|max:32',
			'preparation_time' => 'required|integer|min:1',
			'cooking_time' => 'required|integer|min:1',
			'servings' => 'required|integer|min:1',
			'difficulty' => 'required|string|in:Easy,Medium,Hard',
			'image_path' => 'string',
		]);

		$recipe = Recipe::find($validated['id']);
		if ($recipe->user_id !== auth()->user()->id) {
			return redirect(route('recipes.index'))->withErrors('You can only update your own recipes');
		}

		DB::transaction(function () use ($request, $validated, $recipe): void {
			$recipe->update($validated);
			$recipe->ingredients()->delete();
			$recipe->steps()->delete();
			Ingredient::addIngredientsToRecipe($recipe, $request);
			Step::addStepsToRecipe($recipe, $request);
			if ($request->tags !== null) {
				$recipe->tags()->sync($request->tags);
			}
			User::addReputation(auth()->user(), 1);
		});
		// Updated the recipe information
		$recipe->refresh();

		return redirect()->route('recipes.show', $recipe);
	}

	public function scheduler() {
		return Inertia::render('Recipes/Scheduler');
	}

	public function searchByTerm(string $term): JsonResponse {
		$recipes = Recipe::select('*')
			->where('title', 'LIKE', '%' . $term . '%')
			->with(
				[
					'user',
					'savedUsers' => function ($query): void {
						$query->select('user_id', 'recipe_id');
					},
					'tags',
				]
			)
			->get();

		return response()->json(['recipes' => $recipes]);
	}
}
