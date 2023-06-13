<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use App\Models\Applications;
use App\Models\CandidatesBio;
use App\Models\Vacancies;
use Illuminate\Console\Application;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Mail\ApplicationConfirmation;
use App\Mail\OrderShipped;
use Illuminate\Support\Facades\Mail;
use App\Models\Documents;
use League\CommonMark\Node\Block\Document;

class ApplicationController extends Controller
{
    //

    public function index()
    {
        $id = Auth::id();
        try {
            $applications = Applications::select('applications.status', 'vacancies.title', 'companies.name')
                ->join('candidates_bio', 'candidates_bio.cid', '=', 'applications.applicant_id',)
                ->join('vacancies', 'vacancies.id', '=', "applications.job_id")
                ->join('companies', 'companies.entity_id', '=', 'vacancies.entity_id')
                ->where('applications.applicant_id', $id)->get();
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'Error fetching applications.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['status' => true, 'message' => 'applications fetched successfully', 'data' => $applications], Response::HTTP_OK);
    }

    public function apply(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'job_id' => 'required|integer',
            'cv_id' => 'required',
            'cover_letter' => ''
        ]);


        $validated = $validator->validated();

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Error validating input', 'errors' => $validator->errors()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $applicant_id = Auth::id();

        // check if applicant previously applied to the role.
        $check_app = Applications::where('job_id', $validated['job_id'])
            ->where('applicant_id', $applicant_id)
            ->get();

        if ($check_app->count() > 0) {
            return response()->json(['status' => true, 'message' => 'You have applied to the position'], Response::HTTP_OK);
        }


        $cv = $request->file('cv_id')->store('cv');
        $cover_letter = $request->file('cover_letter')->store('cover_letter');
        $applicant_id = Auth::id();
        $data = [
            'job_id' => $validated['job_id'],
            // 'cv_id' => $cv,
            'status' => "",
            // 'cover_letter' => $cover_letter,
            'applicant_id' =>  $applicant_id
        ];

        try {
            $application = Applications::create($data);
            $cv_data = [
                'document_id' => $cv,
                'type' => 'cv',
                'name' => '',
                'application_id' => $application->id,
                'applicant_id' => $applicant_id
            ];

            $cover_letter_data = [
                'document_id' => $cv,
                'type' => 'cv',
                'name' => '',
                'application_id' => $application->id,
                'applicant_id' => $applicant_id
            ];

            $cv_store = Documents::create($cv_data);
            $cover_letter_store = Documents::create($cover_letter_data);

            $vacancy = Vacancies::select('vacancies.title as jobtitle', 'companies.name as company')
                ->leftjoin('companies', 'vacancies.entity_id', 'companies.entity_id')
                ->where('vacancies.id', $validated['job_id'])
                ->get()->first();

            $bio = CandidatesBio::select('first_name')->where('cid', $applicant_id)->get()->first();
        } catch (\Exception $e) {
            Storage::delete([$cv, $cover_letter]);
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'Error occured'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $email_data = [
            'jobTitle' => $vacancy->jobtitle,
            'company' => $vacancy->company,
            'applicantName' => $bio->first_name
        ];

        Mail::to($request->user())->send(new ApplicationConfirmation($email_data));

        return response()->json(['status' => true, 'message' => 'Application successfull'], Response::HTTP_OK);
    }

    public function viewJob(int $id)
    {
        if (Auth::guard('api')->check()) {
            // user is logged in
            $user = Auth::guard('api')->user();
            // check if applicant previously applied.
            $check_application = Vacancies::select('vacancies.title', 'vacancies.description', 'vacancies.skill', 'vacancies.created_at', 'vacancies.status', 'applications.cv_id', 'applications.cover_letter')
                ->join('applications', 'vacancies.id', '=', 'applications.job_id')
                ->where('applications.applicant_id', $user->id)
                ->get();
            if ($check_application->count() > 0) {
                return response()->json(['status' => true, 'message' => 'you have applied for the job', 'data' => $check_application], Response::HTTP_OK);
            } else {
                $application = Vacancies::find($id);
                return response()->json(['status' => true, 'data' => $application], Response::HTTP_OK);
            }
        } else {
            // user is not logged in (no auth or invalid token)
            $application = Vacancies::find($id);
            return response()->json(['status' => true, 'data' => $application], Response::HTTP_OK);
        }
    }

    public function deleteCV(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cv_id' => 'required',
            'application_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'please select the CV to delete it', 'errors' => $validator->errors()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $validated = $validator->validated();

        try {
            Storage::delete([$cv, $cover_letter]);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'Error deleting CV'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteCoverLeter()
    {
    }
}
