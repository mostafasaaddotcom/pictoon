<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreStoryCoverImagePromptRequest;
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

    /**
     * Store a newly created cover image prompt in storage.
     */
    public function store(StoreStoryCoverImagePromptRequest $request): JsonResponse
    {
        $story = Story::findOrFail($request->validated('story_id'));

        $this->authorize('create', [StoryCoverImagePrompt::class, $story]);

        $prompt = StoryCoverImagePrompt::create([
            'user_id' => $story->user_id,
            'story_id' => $story->id,
            'type' => $request->validated('type'),
            'image_prompt' => $request->validated('image_prompt'),
            'meta_data' => $request->validated('meta_data'),
        ]);

        return response()->json([
            'message' => 'Cover image prompt created successfully',
            'data' => $prompt,
        ], 201);
    }
}
