<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStoryCustomMadeImageRequest extends FormRequest
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
            'story_custom_made_id' => ['required', 'integer', 'exists:story_custom_mades,id'],
            'page_number' => ['required', 'integer', 'min:0'],
            'image_type' => ['required', 'string', Rule::in(['page', 'cover_front', 'cover_back'])],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'image_url' => ['nullable', 'string', 'url', 'max:2048'],
            'status' => ['sometimes', 'string', Rule::in(['pending', 'processing', 'completed', 'failed'])],
        ];
    }
}
