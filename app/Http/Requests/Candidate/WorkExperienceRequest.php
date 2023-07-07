<?php

namespace App\Http\Requests\Candidate;

use Illuminate\Foundation\Http\FormRequest;

class WorkExperienceRequest extends FormRequest
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
            'title' => 'required|string',
            'company' => 'required|string',
            'description' => 'required|string',
            'from' => 'required|date_format:Y-m',
            'to' => 'nullable|date_format:Y-m',
            'workhere' => 'required|integer',
            'location' => 'required|string',
        ];
    }
}
