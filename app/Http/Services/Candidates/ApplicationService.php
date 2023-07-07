<?php

namespace App\Http\Services\Candidates;

use App\Models\Documents;
use App\Models\Vacancies;
use App\Models\Applications;
use App\Models\CandidatesBio;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Services\Candidates\DocumentService;

class ApplicationService
{

    public function getAllApplications()
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

        return $applications;
    }

    public function apply($request, $documentService)
    {


        $applicant_id = Auth::id();

        // check if applicant previously applied to the role.
        $check_app = Applications::where('job_id', $request->input('job_id'))
            ->where('applicant_id', $applicant_id)
            ->get();

        if ($check_app->count() > 0) {
            // return response()->json(["status" => "applied"]);
            abort(400, "You have applied to this position");
        }

        try {

            if (!$request->file('document') == null) {



                $document = $documentService->uploadDocument($request);

                // $cover_letter = $document->getData()->data->id;

                // $email_data = $applicationservice->apply($request, $document['data']['document_id']);
                $cover_letter_id = $document->getData()->data->id;
            }

            $data = [
                'job_id' => $request->input('job_id'),
                'cv_id' => $request->input('cv_id'),
                'coverletter_id' => $cover_letter_id,
                'status' => "",
                'applicant_id' =>  $applicant_id
            ];
            $application = Applications::create($data);

            $vacancy = Vacancies::select('vacancies.title as jobtitle', 'companies.name as company')
                ->leftjoin('companies', 'vacancies.entity_id', 'companies.entity_id')
                ->where('vacancies.id', $request->input('job_id'))
                ->get()->first();

            $bio = CandidatesBio::select('first_name')->where('cid', $applicant_id)->get()->first();
        } catch (\Exception $e) {

            // isset($cover_letter) ? Storage::delete([$cover_letter]) : "";
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'Error occured'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $email_data = [
            'jobTitle' => $vacancy->jobtitle,
            'company' => $vacancy->company,
            'applicantName' => $bio->first_name
        ];

        return response()->json(['status' => true, "data" => $email_data]);
    }

    public function viewJob($id)
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
}
