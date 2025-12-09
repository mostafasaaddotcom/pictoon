<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Story;
use App\Models\StoryCoverImagePrompt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoryCoverImagePromptController extends Controller
{
    /**
     * Get all cover image prompts for a story.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'story_id' => ['required', 'integer', 'exists:stories,id'],
        ]);

        $story = Story::findOrFail($request->query('story_id'));

        $this->authorize('viewAny', [StoryCoverImagePrompt::class, $story]);

        $prompts = StoryCoverImagePrompt::where('story_id', $story->id)
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json([
            'data' => $prompts,
        ]);
    }
}
