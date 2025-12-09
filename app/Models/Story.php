<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Story extends Model
{
    /** @use HasFactory<\Database\Factories\StoryFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'idea',
        'description',
        'moral_lesson',
        'language',
        'pages_count',
        'images',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'images' => 'array',
            'is_active' => 'boolean',
            'pages_count' => 'integer',
        ];
    }

    /**
     * Get the user that owns the story.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include stories for a specific user.
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * Get the page image prompts for the story.
     */
    public function pageImagePrompts(): HasMany
    {
        return $this->hasMany(StoryPageImagePrompt::class);
    }

    /**
     * Get the cover image prompts for the story.
     */
    public function coverImagePrompts(): HasMany
    {
        return $this->hasMany(StoryCoverImagePrompt::class);
    }

    /**
     * Get the custom made stories for the story.
     */
    public function customMades(): HasMany
    {
        return $this->hasMany(StoryCustomMade::class);
    }
}
