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

        $applicant_id = Auth::id();


        try {

            if (!$request->file('cover_letter') == null) {
                $cover_letter = $request->file('cover_letter')->store('cover_letter');

                $cover_letter_data = [
                    'document_id' => $cover_letter,
                    'type' => 'cover_letter',
                    'name' => '',
                    'applicant_id' => $applicant_id
                ];

                $cover_letter_store = Documents::create($cover_letter_data);

                $cover_letter = Documents::where('document_id', $cover_letter)->first();
                $cover_letter_id = $cover_letter->id;
            } else {
                $cover_letter_id = '';
            }
            // if (isset($request->file('cover_letter')))

            $data = [
                'job_id' => $validated['job_id'],
                'cv_id' => $validated['cv_id'],
                'coverletter_id' => $cover_letter_id,
                'status' => "",
                'applicant_id' =>  $applicant_id
            ];
            $application = Applications::create($data);

            $vacancy = Vacancies::select('vacancies.title as jobtitle', 'companies.name as company')
                ->leftjoin('companies', 'vacancies.entity_id', 'companies.entity_id')
                ->where('vacancies.id', $validated['job_id'])
                ->get()->first();

            $bio = CandidatesBio::select('first_name')->where('cid', $applicant_id)->get()->first();
        } catch (\Exception $e) {

            isset($cover_letter) ? Storage::delete([$cover_letter]) : "";
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
            $check_application = Vacancies::select('vacancies.title', 'vacancies.description', 'vacancies.skill', 'vacancies.created_at', 'vacancies.status', 'applications.cv_id')
                ->join('applications', 'vacancies.id', '=', 'applications.job_id')
                ->where('applications.applicant_id', $user->id)
                ->get();

            if ($check_application->count() > 0) {
                return response()->json(['status' => true, 'message' => 'you have applied for the job', 'data' => $check_application], Response::HTTP_OK);
            } else {
                $vancancy = Vacancies::find($id);
                // get last updated cv
                $last_cv = Documents::where('applicant_id', $user->id)
                    ->where('type', 'cv')
                    ->get()
                    ->last();
                $data = [
                    'vacancy' => $vancancy,
                    'cv' => $last_cv->document_id
                ];
                return response()->json(['status' => true, 'data' => $data], Response::HTTP_OK);
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

    public function uploadDocument(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'cv' => 'required',
        ]);


        $validated = $validator->validated();

        $cv = $request->file('cv')->store('cv');
        $applicant_id = Auth::id();

        try {
            $cv_data = [
                'document_id' => $cv,
                'type' => 'cv',
                'name' => '',
                'applicant_id' => $applicant_id
            ];

            $cv_store = Documents::create($cv_data);
            return response()->json(['status' => true, 'message' => 'CV Uploaded Successfully', 'data' => $cv], Response::HTTP_OK);
        } catch (\Exception $e) {
            Storage::delete([$cv]);
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'Error occured'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
