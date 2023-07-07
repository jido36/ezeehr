<?php

namespace App\Http\Services\Candidates;

use App\Models\Education;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class EducationService
{
    public function store($request)
    {
        $data = [
            'degree' => $request->input('degree'),
            'course' => $request->input('course'),
            'school' => $request->input('school'),
            'from' => $request->input('from'),
            'to' => $request->input('to'),
            'user_id' => Auth::id()
        ];

        try {
            Education::create($data);
        } catch (\Exception $e) {
            Log::error($e);
        }
    }
}
