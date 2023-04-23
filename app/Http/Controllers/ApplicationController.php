<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use App\Models\Applications;
use App\Models\Jobs;
use Illuminate\Console\Application;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    //

    public function index()
    {
        $id = Auth::id();
        try {
            $applications = Applications::select('applications.status', 'jobs.title', 'companies.name')
                ->join('candidates_bio', 'candidates_bio.cid', '=', 'applications.applicant_id',)
                ->join('jobs', 'jobs.id', '=', "applications.job_id")
                ->join('companies', 'companies.entity_id', '=', 'jobs.entity_id')
                ->where('applications.applicant_id', $id)->get();
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'Error fetching applications.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['status' => true, 'message' => 'applications fetched successfully', 'data' => $applications], Response::HTTP_OK);
    }

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
            'cv_id' => 'required',
            'cover_letter' => ''
        ]);


        $validated = $validator->validated();

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Error validating input', 'errors' => $validator->errors()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $cv = $request->file('cv_id')->store('cv');
        $cover_letter = $request->file('cover_letter')->store('cover_letter');
        $applicant_id = Auth::id();
        $data = [
            'job_id' => $validated['job_id'],
            'cv_id' => $cv,
            'status' => "",
            'cover_letter' => $cover_letter,
            'applicant_id' =>  $applicant_id
        ];

        // check if applicant previously applied to the role.
        $check_app = Applications::where('job_id', $validated['job_id'])
            ->where('applicant_id', $applicant_id)
            ->get();

        if ($check_app->count() > 0) {
            return response()->json(['status' => true, 'message' => 'You have applied to the position'], Response::HTTP_OK);
        }

        try {
            Applications::create($data);
        } catch (\Exception $e) {
            Storage::delete([$cv, $cover_letter]);
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'Error occured'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['status' => true, 'message' => 'Job added successfully'], Response::HTTP_OK);
    }

    public function viewJob(int $id)
    {

        if (Auth::guard('api')->check()) {
            // user is logged in
            $user = Auth::guard('api')->user();
            // check if applicant previously applied.
            $check_application = Jobs::select('jobs.title', 'jobs.description', 'jobs.skill', 'jobs.created_at', 'jobs.status', 'applications.cv_id', 'applications.cover_letter')
                ->join('applications', 'jobs.id', '=', 'applications.job_id')
                ->where('applications.applicant_id', $user->id)
                ->get();
            if ($check_application->count() > 0) {
                return response()->json(['status' => true, 'message' => 'you have applied for the job', 'data' => $check_application], Response::HTTP_OK);
            } else {
                $application = Jobs::find($id);
                return response()->json(['status' => true, 'data' => $application], Response::HTTP_OK);
            }
        } else {
            // user is not logged in (no auth or invalid token)
            $application = Jobs::find($id);
            return response()->json(['status' => true, 'data' => $application], Response::HTTP_OK);
        }
    }
}
