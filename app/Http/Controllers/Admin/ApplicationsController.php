<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Admin\Comment;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Services\Admin\ApplyService;
use App\Http\Services\Admin\AuthorisationService;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Services\Admin\ActivityLogService;
use App\Models\Admin\ActivityLog;
use App\Models\Admin\Applications as AdminApplications;
use Exception;
use App\Jobs\CommentAdded;

class ApplicationsController extends Controller
{
    //
    public function listAllApplications(ApplyService $ApplyService)
    {
        return $ApplyService->listAllApplications();
    }

    public function getCandidateApplication(Request $request, ApplyService $ApplyService)
    {
        $application_id = $request->input('app_id');

        $candidate_application = $ApplyService->getCandidateApplication($application_id);

        return $candidate_application;
    }

    public function addComment(Request $request, ApplyService $ApplyService)
    {
        $comment = $ApplyService->addComment($request);

        return $comment;
    }

    public function updateApplication(Request $request, ApplyService $ApplyService, AuthorisationService $authorisationservice, ActivityLogService $activity)
    {
        // implement policy for application update.
        try {
            $authorisationservice->updateApplication();
        } catch (Exception $e) {
            abort(400, $e->getMessage());
        }


        $update_application = $ApplyService->updateApplication($request);

        if (isset($update_application['app_id'])) {
            $activity->log("Application Update", "Application", $update_application['app_id'], $update_application['admin_id']);
        }

        return response()->json(['status' => true, 'message' => 'Application updated successfully'], Response::HTTP_OK);
    }
}
