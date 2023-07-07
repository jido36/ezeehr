<?php

namespace App\Http\Services\Candidates;

use App\Models\CandidatesBio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;

class BioService
{
    public function store($request)
    {
        $id = Auth::id();
        $data = [
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'dob' => $request->input('dob'),
            'sex' => $request->input('sex'),
            'cid' => $id,
        ];

        try {
            // check if candidate exit
            $checkCandidate = CandidatesBio::find($id);
            if ($checkCandidate->count() > 0) {
                CandidatesBio::find($id)
                    ->update($data);
            } else {
                CandidatesBio::Create($data);
            };
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'Error occured'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
