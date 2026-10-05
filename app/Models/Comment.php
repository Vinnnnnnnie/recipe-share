<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model {
    /** @use HasFactory<CommentFactory> */
    use HasFactory;

    protected $fillable = ['comment', 'user_id', 'recipe_id'];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function recipe(): BelongsTo {
        return $this->belongsTo(Recipe::class);
    }
}
