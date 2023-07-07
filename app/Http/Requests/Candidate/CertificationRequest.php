<?php

namespace App\Http\Requests\Candidate;

use Illuminate\Foundation\Http\FormRequest;

class CertificationRequest extends FormRequest
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
            'name' => 'required|string',
            'organisation' => 'required|string',
            'issue_date' => 'required|date_format:Y-m',
            'expiry_date' => 'nullable|date_format:Y-m',
        ];
    }
}
