<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Post;

class StorePostRequest extends FormRequest
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
        $maxTitle = Post::MAX_TITLE_SIZE;
        $maxBody = Post::MAX_BODY_SIZE;
        $maxExcerpt = Post::MAX_EXCERPT_SIZE;

        return [
            'title' => "required|max:$maxTitle",
            'body' => "required|max:$maxBody",
            'excerpt' => "required|max:$maxExcerpt",
            'category_id' => 'required|int|exists:categories,id',
        ];
    }
}
