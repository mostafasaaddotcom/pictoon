<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Story;
use App\Models\StoryPageImagePrompt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoryPageImagePromptController extends Controller
{
    /**
     * Get all page image prompts for a story.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'story_id' => ['required', 'integer', 'exists:stories,id'],
        ]);

        $story = Story::findOrFail($request->query('story_id'));

        $this->authorize('viewAny', [StoryPageImagePrompt::class, $story]);

        $prompts = StoryPageImagePrompt::where('story_id', $story->id)
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json([
            'data' => $prompts,
        ]);
    }
}
