<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\RecipeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class Recipe extends Model {
    /** @use HasFactory<RecipeFactory> */
    use HasFactory;

    protected $fillable = ['title', 'preparation_time', 'cooking_time', 'servings', 'difficulty', 'image_path', 'user_id'];

    public function random() {
        return Recipe::inRandomOrder()->first();
    }

    public function getRecipeById(int $id): Recipe {
        return Recipe::find($id, 'recipe_id');
    }

    /**
     * @return LengthAwarePaginator
     */
    public function getLatestRecipesPaginated(): LengthAwarePaginator {
        return Recipe::with(
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
            ->paginate();
    }

    /**
     * @return HasMany<Comment, $this>
     */
    public function comments(): HasMany {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * returns all tags from Pivot
     * @return BelongsToMany<Tag, $this, Pivot>
     */
    public function tags(): BelongsToMany {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Users who have saved recipe
     * @return BelongsToMany<User, $this, Pivot>
     */
    public function savedUsers(): BelongsToMany {
        return $this->belongsToMany(User::class, 'saved_recipes');
    }

    /**
     * Steps for the recipe
     * @return HasMany<Step, $this>
     */
    public function steps(): HasMany {
        return $this->hasMany(Step::class);
    }

    /**
     * Ingredients for recipe
     * @return BelongsToMany<Ingredient, $this>
     */
    public function ingredients(): BelongsToMany {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredient', 'recipe_id', 'ingredient_id')->withPivot('quantity', 'measurement', 'order');
    }
}
