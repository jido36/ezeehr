<?php

namespace App\Http\Requests\Candidate;

use Illuminate\Foundation\Http\FormRequest;

class EducationRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'degree' => 'required|string',
            'course' => 'required|string',
            'school' => 'required|string',
            'from' => 'required|date_format:Y',
            'to' => 'required|date_format:Y'
        ];
    }
}
