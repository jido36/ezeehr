<?php

namespace App\Http\Services\Admin;

use App\Jobs\CommentAdded;
use App\Models\Admin\Stage;
use App\Models\Admin\Comment;
use Illuminate\Http\Response;
use App\Models\Admin\Applications;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Database\Eloquent\Builder;
use App\Models\Admin\Applications as AdminApplications;

class ApplyService
{
    protected $user;

    public function listAllApplications()
    {
        $this->user = Auth::user();
        try {
            $applications = Applications::with(['vacancies' => function (Builder $query) {
                $query->select('id', 'title');
                $query->where('entity_id', '=', $this->user->entity_id);
            }, 'candidateBio:id,first_name,last_name,sex'])
                // ->where('vacancies.entity_id', '=', $user->entity_id)
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

    public function getCandidateApplication($application_id)
    {

        try {
            $get_application = Applications::with(['comments', 'vacancies:id,title,stage_type_id', 'certifications', 'applicationCv:id,document_id', 'applicationCoverLetter:id,document_id'])
                ->where('id', '=', $application_id)->first();
            $stage_id = $get_application->vacancies->stage_type_id;
            $stages = Stage::select('id', 'title')
                ->where('stage_type_id', $stage_id)->get();
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'Error fetching application data, kindly contact the site admin'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $data = [
            'application' => $get_application,
            'stages' => $stages
        ];

        return response()->json(['status' => true, 'message' => 'applications fetched successfully', 'data' => $data], Response::HTTP_OK);
    }

    public function addComment($request)
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
            $comment = Comment::create($data);
            CommentAdded::dispatch($comment);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'Error adding comment, kindly contact the site admin'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json(['status' => true, 'message' => 'comment added successfully', 'data' => $request->all()], Response::HTTP_OK);
    }

    public function updateApplication($request)
    {

        $validator = Validator::make($request->all(), [
            'status' => 'required|string',
        ]);

        $validated = $validator->validated();

        $application_id = $request->input('app_id');

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

        $data = [
            'app_id' => $application_id,
            'admin_id' => $admin_id
        ];

        return $data;
    }
}
