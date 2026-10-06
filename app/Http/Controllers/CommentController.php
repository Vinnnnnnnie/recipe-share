<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Http\Request;

class CommentController extends Controller {
	public function store(Request $request) {
		$request->validate([
			'recipe_id' => 'required|exists:recipes,id',
			'comment' => 'required|string|max:1000',
		]);

		Comment::create([
			'recipe_id' => $request->input('recipe_id'),
			'user_id' => auth()->user()->id,
			'comment' => $request->input('comment'),
		]);
		User::addReputation(auth()->user(), 1);
		$recipeOwner = Recipe::find($request->input('recipe_id'), 'user_id');
		User::addReputation($recipeOwner->user_id, 1);

		return redirect()->back()->with('success', 'Comment added successfully!');
	}

	public function destroy(Comment $comment) {
		if ($comment->user_id === auth()->user()->id) {
			User::subtractReputation($comment->user_id, 1);
			$recipeOwner = Recipe::find($comment->recipe_id, 'user_id');
			User::subtractReputation($recipeOwner->user_id, 1);

			$comment->delete();
			return response()->json('Comment deleted!');
		} else {
			return response()->json('Operation not permitted!');
		}
	}
}
