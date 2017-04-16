<?php

namespace App\Http\Requests\Webapi;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePhotoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'is_avatar' => 'sometimes|required|boolean',
            'is_public' => 'sometimes|required|boolean'
        ];
    }

    /**
     * Custom error messages.
     * 
     * @return array
     */
    public function messages()
    {
        return [
            'is_public.required' => 'The public field is required.',
            'is_public.boolean' => 'The public field must be true or false.',
            'is_avatar.required' => 'The avatar field is required.',
            'is_avatar.boolean' => 'The avatar field must be true or false.'
        ];
    }
}
