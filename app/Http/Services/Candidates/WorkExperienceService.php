<?php

namespace App\Http\Services\Candidates;

use App\Models\WorkExperience;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class WorkExperienceService
{

    public function store($request)
    {
        $data = [
            'title' => $request->input('title'),
            'company' => $request->input('company'),
            'description' => $request->input('description'),
            'from' => $request->input('from') . '-01',
            'to' => is_null($request->input('to')) ? null : $request->input('to'),
            'workhere' => $request->input('workhere'),
            'location' => $request->input('location'),
            'user_id' => Auth::id()
        ];

        try {
            WorkExperience::create($data);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'Error adding work experience'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
