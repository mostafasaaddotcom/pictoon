<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoryCustomMade extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'story_id',
        'child_name',
        'child_gender',
        'child_age',
        'child_image_url',
        'pdf_final_url',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'child_age' => 'integer',
        ];
    }

    /**
     * Get the user that owns the custom made story.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the story that owns the custom made story.
     */
    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }

    /**
     * Scope a query to only include custom mades for a given story.
     */
    public function scopeForStory(Builder $query, Story $story): Builder
    {
        return $query->where('story_id', $story->id);
    }

    /**
     * Get the images for the custom made story.
     */
    public function images(): HasMany
    {
        return $this->hasMany(StoryCustomMadeImage::class);
    }
}
