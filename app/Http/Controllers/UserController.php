<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Recipe;
use Inertia\Inertia;

class UserController extends Controller {
    public function show(User $user) {
        return Inertia::render(
            'Users/Show',
            ['user' => $user,
                'recipes' => Inertia::scroll(
                    fn() => Recipe::with(
                        [
                            'user',
                            'comments:id,user_id,recipe_id',
                            'tags',
                            'savedUsers' => function ($query): void {
                                $query->select('user_id', 'recipe_id');
                            },
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
                        ->where('user_id', $user->id)
                        ->orderBy('created_at', 'desc')->paginate()
                ),
            ]
        );
    }

    public function follow(Request $request) {
        $userToFollow = $request->id;
        $user = auth()->user();

        $user->following()->attach($userToFollow);
        return response()->json('User Followed!');

    }

    public function unfollow(Request $request) {
        $userToUnfollow = $request->id;
        $user = auth()->user();

        $user->following()->detach($userToUnfollow);
        return response()->json('User Unfollowed!');

    }

    public function edit() {
        return Inertia::render('Users/Edit');
    }

    public function addSavedRecipe(Recipe $recipe) {
        $user = auth()->user();

        $user->savedRecipes()->attach($recipe->id);
        User::addReputation($recipe->user, 1);
        return response()->json('Added to your saved recipes!', 200);
    }

    public function removeSavedRecipe(Recipe $recipe) {
        $user = auth()->user();
        $user->savedRecipes()->detach($recipe->id);
        User::subtractReputation($recipe->user, 1);
        return response()->json('Removed from your saved recipes!', 200);
    }

    public function savedRecipes() {
        return Inertia::render(
            'Users/SavedRecipes',
            ['recipes' => Inertia::scroll(
                fn() => auth()
                    ->user()
                    ->savedRecipes()
                    ->with(
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
                    ->orderBy('created_at', 'desc')
                    ->paginate()
            )]
        );
    }

    public function update(Request $request) {
        if ($request->image) {
            $image_path = $request->image->store("users", 'public');
            $image_path = str_replace('users/', '', $image_path);
            $request->merge(['image_path' => $image_path]);
        }
        $validated = $request->validate([
            'first_name' => 'required|string|max:32',
            'last_name' => 'required|string|max:32',
            'bio' => 'nullable|string|max:255',
            'image_path' => 'string',
        ]);
        $user = User::find(auth()->user()->id);

        $user->update($validated);
        $user->refresh();
        return redirect()->route('users.show', $user)->with('success', 'Profile updated successfully!');
    }

    public function settings() {
        return Inertia::render('Users/Settings');
    }
}
