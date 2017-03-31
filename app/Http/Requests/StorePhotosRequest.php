<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePhotosRequest extends FormRequest
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
            'photos' => 'required|array|max:5',
            'photos.*' => 'required|mimes:jpg,jpeg,png,bmp|max:2000'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'photos.*.required' => 'Please upload an image.',
            'photos.*.mimes' => 'Only jpeg, png and bmp images are allowed.',
            'photos.*.max' => 'Maximum allowed size for one image is 2MB.'
        ];
    }
}
