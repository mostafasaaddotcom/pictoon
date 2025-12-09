<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoryPageImagePrompt extends Model
{
    /** @use HasFactory<\Database\Factories\StoryPageImagePromptFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'story_id',
        'page_number',
        'scene_title',
        'image_prompt',
        'story_text',
        'emotions',
        'art_style',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'page_number' => 'integer',
        ];
    }

    /**
     * Get the user that owns the page prompt.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the story that owns the page prompt.
     */
    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }

    /**
     * Scope a query to only include prompts for a specific story.
     */
    public function scopeForStory(Builder $query, Story $story): Builder
    {
        return $query->where('story_id', $story->id);
    }
}
