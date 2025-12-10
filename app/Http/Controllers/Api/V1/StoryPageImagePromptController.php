<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreStoryPageImagePromptRequest;
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

    /**
     * Store a newly created page image prompt in storage.
     */
    public function store(StoreStoryPageImagePromptRequest $request): JsonResponse
    {
        $story = Story::findOrFail($request->validated('story_id'));

        $this->authorize('create', [StoryPageImagePrompt::class, $story]);

        $prompt = StoryPageImagePrompt::create([
            'user_id' => $story->user_id,
            'story_id' => $story->id,
            'page_number' => $request->validated('page_number'),
            'scene_title' => $request->validated('scene_title'),
            'image_prompt' => $request->validated('image_prompt'),
            'story_text' => $request->validated('story_text'),
            'emotions' => $request->validated('emotions'),
            'art_style' => $request->validated('art_style'),
        ]);

        return response()->json([
            'message' => 'Page image prompt created successfully',
            'data' => $prompt,
        ], 201);
    }
}
