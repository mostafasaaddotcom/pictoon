<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoryCustomMadeImage extends Model
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
        'story_custom_made_id',
        'page_number',
        'image_type',
        'reference_number',
        'image_url',
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
            'page_number' => 'integer',
        ];
    }

    /**
     * Get the user that owns the image.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the story that owns the image.
     */
    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }

    /**
     * Get the custom made that owns the image.
     */
    public function customMade(): BelongsTo
    {
        return $this->belongsTo(StoryCustomMade::class, 'story_custom_made_id');
    }

    /**
     * Scope a query to only include images for a given custom made.
     */
    public function scopeForCustomMade(Builder $query, StoryCustomMade $customMade): Builder
    {
        return $query->where('story_custom_made_id', $customMade->id);
    }
}
