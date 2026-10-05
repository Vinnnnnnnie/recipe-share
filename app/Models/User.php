<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'email',
        'bio',
        'password',
        'image_path',
        'reputation',
    ];
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function addReputation(User|int $user, int $reputation): void {
        // If given ID instead of User object
        if (is_int($user)) {
            $user = User::find($user, ['id', 'reputation']);
        }

        $newReputation = $user->reputation + $reputation;
        $user->update(['reputation' => $newReputation]);
    }

    public static function subtractReputation(User|int $user, int $reputation): void {
        // If given ID instead of User object
        if (is_int($user)) {
            $user = User::find($user, ['id', 'reputation']);
        }

        $newReputation = $user->reputation - $reputation;
        $user->update(['reputation' => $newReputation]);
    }

    public function totalReputation(): int {
        return 10;
        // Add number of recipes
        // Add number of comments
    }

    public function recipes() {
        return $this->hasMany(Recipe::class);
    }

    public function followers() {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id')->withTimestamps();
    }

    public function following() {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id')->withTimestamps();
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function savedRecipes() {
        return $this->belongsToMany(Recipe::class, 'saved_recipes');
    }
}
