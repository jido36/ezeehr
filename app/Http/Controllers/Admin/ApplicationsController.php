<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Applications as AdminApplications;
use Illuminate\Http\Request;
use App\Models\Applications;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\Comment;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Admin\ActivityLogController;

class ApplicationsController extends Controller
{
    //
    public function listAllApplications()
    {
        try {
            $applications = Applications::selectRaw('jobs.title, CONCAT(candidates_bio.first_name, " ", candidates_bio.last_name) AS full_name')
                ->join('jobs', 'applications.job_id', '=', 'jobs.id')
                ->join('companies', 'jobs.entity_id', '=', 'companies.entity_id')
                ->join('candidates_bio', 'applications.applicant_id', 'candidates_bio.cid')
                ->simplePaginate(15);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'Error fetching data, kindly contact the site admin'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $no_of_applications = $applications->count();
        if ($no_of_applications == 0) {
            return response()->json(['status' => true, 'message' => 'No candidates applications yet', 'data' => $applications], Response::HTTP_OK);
        } else {
            return response()->json(['status' => true, 'message' => 'applications fetched successfully', 'data' => $applications], Response::HTTP_OK);
        }
    }

    public function getCandidateApplication(Request $request)
    {
        $application_id = $request->input('app_id');
        // echo $application_id;
        // die;
        try {
            $get_application = Applications::selectRaw('jobs.title, CONCAT(candidates_bio.first_name, " ", candidates_bio.last_name) AS full_name')
                ->leftJoin('jobs', 'applications.job_id', '=', 'jobs.id')
                ->leftJoin('companies', 'jobs.entity_id', '=', 'companies.entity_id')
                ->leftJoin('candidates_bio', 'applications.applicant_id', 'candidates_bio.cid')
                ->leftJoin('education', 'candidates_bio.cid', 'education.user_id')
                ->leftJoin('certifications', 'candidates_bio.cid', 'certifications.user_id')
                ->leftJoin('links', 'candidates_bio.cid', 'links.user_id')
                ->where('applications.id', '=', $application_id)
                ->get();
            // ->toSql();
            $comments = Comment::selectRaw('comments, application_comments.created_at, CONCAT(admin_users.first_name, " ", admin_users.last_name) AS full_name')
                // where('application_id', $application_id)
                ->leftJoin('admin_users', 'application_comments.admin_id', 'admin_users.id')->get();
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'Error fetching application data, kindly contact the site admin'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // print_r($get_application);
        // die;
        $data = [
            'application' => $get_application,
            'comments' => $comments
        ];

        return response()->json(['status' => true, 'message' => 'applications fetched successfully', 'data' => $data], Response::HTTP_OK);
    }

    public function addComment(Request $request)
    {
        $application_id = $request->input('app_id');
        $user_id = Auth::id();

        $validator = Validator::make($request->all(), [
            'comment' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'comment cannot be empty', 'errors' => $validator->errors()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $validated = $validator->validated();

        $data = [
            'application_id' =>  $application_id,
            'comments' => $validated['comment'],
            'admin_id' => $user_id
        ];

        try {
            Comment::create($data);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'Error adding comment, kindly contact the site admin'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json(['status' => true, 'message' => 'comment added successfully', 'data' => $request->all()], Response::HTTP_OK);
    }

    public function updateApplication(Request $request, ActivityLogController $activity)
    {
        // implement policy for application update.

        $application_id = $request->input('app_id');

        $validator = Validator::make($request->all(), [
            'status' => 'required|string',
        ]);

        $validated = $validator->validated();

        $data = [
            'status' => $validated['status']
        ];
        try {
            AdminApplications::find($application_id)->update($data);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'Error updating application, kindly contact the site admin'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $admin_id = Auth::id();
        $activity->log("Application Update", "Application", $application_id, $admin_id);
        return response()->json(['status' => true, 'message' => 'Application upated successfully'], Response::HTTP_OK);
    }
}
