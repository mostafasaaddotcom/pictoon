<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UpdateStoryCustomMadeRequest;
use App\Models\StoryCustomMade;
use Illuminate\Http\JsonResponse;

class StoryCustomMadeController extends Controller
{
    /**
     * Update the specified custom made story.
     */
    public function update(UpdateStoryCustomMadeRequest $request, StoryCustomMade $storyCustomMade): JsonResponse
    {
        $this->authorize('update', $storyCustomMade);

        $storyCustomMade->update($request->validated());

        return response()->json([
            'message' => 'Story custom made updated successfully.',
            'data' => $storyCustomMade->fresh(),
        ]);
    }
}
