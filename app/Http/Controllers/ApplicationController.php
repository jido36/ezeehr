<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use App\Models\Applications;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    //

    public function apply(Request $request)
    {
        /**
         * application id
         * applicant id
         * cv
         * cover letter
         *
         */
        $validator = Validator::make($request->all(), [
            'job_id' => 'required|integer',
            // 'cv_id' => 'required|uuid',
            // 'cover_letter_id' => 'required|uuid'
            'cv_id' => 'required',
            'cover_letter' => 'required'
        ]);


        $validated = $validator->validated();

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Error validating input', 'errors' => $validator->errors()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $data = [
            'job_id' => $validated['job_id'],
            // 'cv_id' => $validated['cv_id'],
            'cv_id' => (string) Str::uuid(),
            // Str::uuid();
            // 'cover_letter_id' => $validated['cover_letter_id'],
            'cover_letter' => (string) Str::uuid(),
            'applicant_id' =>  Auth::id()
        ];

        try {
            Applications::create($data);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'Error occured'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['status' => true, 'message' => 'Job added successfully', 'data' => $request->all()], Response::HTTP_OK);
    }
}
