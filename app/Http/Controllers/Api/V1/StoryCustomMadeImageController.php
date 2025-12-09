<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreStoryCustomMadeImageRequest;
use App\Http\Requests\Api\V1\UpdateStoryCustomMadeImageRequest;
use App\Models\StoryCustomMade;
use App\Models\StoryCustomMadeImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StoryCustomMadeImageController extends Controller
{
    /**
     * Get all custom made images for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'status' => ['sometimes', 'string', Rule::in(['pending', 'processing', 'completed', 'failed'])],
            'story_custom_made_id' => ['sometimes', 'integer', 'exists:story_custom_mades,id'],
        ]);

        $query = StoryCustomMadeImage::where('user_id', $request->user()->id);

        if ($request->has('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->has('story_custom_made_id')) {
            $query->where('story_custom_made_id', $request->query('story_custom_made_id'));
        }

        return response()->json([
            'data' => $query->get(),
        ]);
    }

    /**
     * Store a newly created image in storage.
     */
    public function store(StoreStoryCustomMadeImageRequest $request): JsonResponse
    {
        $customMade = StoryCustomMade::findOrFail($request->validated('story_custom_made_id'));

        $this->authorize('create', [StoryCustomMadeImage::class, $customMade]);

        $image = StoryCustomMadeImage::create([
            'user_id' => $customMade->user_id,
            'story_id' => $customMade->story_id,
            'story_custom_made_id' => $customMade->id,
            'page_number' => $request->validated('page_number'),
            'image_type' => $request->validated('image_type'),
            'reference_number' => $request->validated('reference_number'),
            'image_url' => $request->validated('image_url'),
            'status' => $request->validated('status', 'pending'),
        ]);

        return response()->json([
            'message' => 'Image created successfully',
            'data' => $image,
        ], 201);
    }

    /**
     * Get an image by reference number.
     */
    public function showByReference(string $referenceNumber): JsonResponse
    {
        $image = StoryCustomMadeImage::where('reference_number', $referenceNumber)->firstOrFail();

        $this->authorize('view', $image);

        return response()->json([
            'data' => $image,
        ]);
    }

    /**
     * Update the specified image in storage.
     */
    public function update(UpdateStoryCustomMadeImageRequest $request, StoryCustomMadeImage $image): JsonResponse
    {
        $this->authorize('update', $image);

        $image->update($request->validated());

        return response()->json([
            'message' => 'Image updated successfully',
            'data' => $image->fresh(),
        ]);
    }
}
