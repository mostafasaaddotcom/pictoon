<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreStoryPageImagePromptRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'story_id' => ['required', 'integer', 'exists:stories,id'],
            'page_number' => ['required', 'integer', 'min:1'],
            'scene_title' => ['required', 'string', 'max:255'],
            'image_prompt' => ['required', 'string'],
            'story_text' => ['required', 'string'],
            'emotions' => ['nullable', 'string'],
            'art_style' => ['required', 'string', 'max:255'],
        ];
    }
}
