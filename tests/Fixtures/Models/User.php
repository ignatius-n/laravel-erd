<?php

namespace Recca0120\LaravelErd\Tests\Fixtures\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Permission\Traits\HasRoles;

class User extends Model
{
    use HasRoles;
    use Compoships;

    protected $fillable = ['name', 'email', 'password'];

    /**
     * Get the phone associated with the user.
     */
    public function phone(): HasOne
    {
        return $this->hasOne(Phone::class);
    }

    public function latestPost(): HasOne
    {
        return $this->hasOne(Post::class)->latestOfMany();
    }

    public function oldestPost(): HasOne
    {
        return $this->hasOne(Post::class)->oldestOfMany();
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the user's image.
     */
    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    /**
     * Get the user's images.
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(Device::class, 'user_device');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, ['team_id', 'category_id'], ['team_id', 'category_id']);
    }
}
