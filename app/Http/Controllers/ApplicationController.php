<?php

namespace App\Http\Controllers;

use App\Http\Requests\Candidate\ApplyRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Mail\ApplicationConfirmation;
use Illuminate\Support\Facades\Mail;
use App\Http\Services\Candidates\ApplicationService;
use App\Http\Services\Candidates\DocumentService;

class ApplicationController extends Controller
{
    //

    public function index(ApplicationService $applicationservice)
    {
        $applications = $applicationservice->getAllApplications();

        return response()->json(['status' => true, 'message' => 'applications fetched successfully', 'data' => $applications], Response::HTTP_OK);
    }


    public function apply(ApplyRequest $request, ApplicationService $applicationservice, DocumentService $documentService)
    {
        $email_data = $applicationservice->apply($request, $documentService);

        $ret_email_data = $email_data->getData();

        Mail::to($request->user())->send(new ApplicationConfirmation($ret_email_data->data));

        return response()->json(['status' => true, 'message' => 'Application successfull'], Response::HTTP_OK);
    }

    public function viewJob(int $id, ApplicationService $applicationservice)
    {
        return $applicationservice->viewJob($id);
    }

    public function deleteDocument(Request $request, DocumentService $documentservice)
    {

        $documentservice->deleteDocument($request);

        return response()->json(['status' => true, 'message' => 'CV deleted'], Response::HTTP_OK);
    }

    public function deleteCoverLeter()
    {
    }

    public function uploadDocument(Request $request, DocumentService $documentService)
    {

        return $documentService->uploadDocument($request);
    }
}
