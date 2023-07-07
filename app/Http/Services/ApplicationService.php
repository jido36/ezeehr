<?php

namespace App\Http\Services\Admin;

use Illuminate\Http\Response;
use App\Models\Admin\Applications;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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

        return response()->json(['status' => true, 'message' => 'applications fetched successfully', 'data' => $applications], Response::HTTP_OK);
    }
}
