<?php

use App\Http\Controllers\Api\V1\StoryCoverImagePromptController;
use App\Http\Controllers\Api\V1\StoryCustomMadeController;
use App\Http\Controllers\Api\V1\StoryCustomMadeImageController;
use App\Http\Controllers\Api\V1\StoryPageImagePromptController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Story Page Image Prompts
    Route::get('story-page-image-prompts', [StoryPageImagePromptController::class, 'index']);

    // Story Cover Image Prompts
    Route::get('story-cover-image-prompts', [StoryCoverImagePromptController::class, 'index']);

    // Story Custom Made Images
    Route::get('story-custom-made-images', [StoryCustomMadeImageController::class, 'index']);
    Route::get('story-custom-made-images/reference/{referenceNumber}', [StoryCustomMadeImageController::class, 'showByReference']);
    Route::post('story-custom-made-images', [StoryCustomMadeImageController::class, 'store']);
    Route::put('story-custom-made-images/{image}', [StoryCustomMadeImageController::class, 'update']);
    Route::patch('story-custom-made-images/{image}', [StoryCustomMadeImageController::class, 'update']);

    // Story Custom Mades
    Route::put('story-custom-mades/{storyCustomMade}', [StoryCustomMadeController::class, 'update']);
    Route::patch('story-custom-mades/{storyCustomMade}', [StoryCustomMadeController::class, 'update']);
});
