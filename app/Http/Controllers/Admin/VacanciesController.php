<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Models\Admin\Vacancies;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class VacanciesController extends Controller
{

    public function index()
    {
        $response = Gate::inspect('viewJobs', Jobs::class);

        if (!$response->allowed()) {
            return response()->json(['status' => false, 'message' => $response->message()], 403);
        }

        try {
            $user = Auth::user();
            $listJobs = Vacancies::where('entity_id', $user->entity_id)->paginate(20);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'Error fetching jobs please contact admin'], 403);
        }
        return response()->json(['status' => true, 'data' => $listJobs], 200);
    }

    public function createJob(Request $request)
    {
        // print_r($request->all());
        // die;
        $validated = $request->validate(
            [
                'title' => 'required|string',
                'description' => 'required|string',
                'skill' => 'string',
                'status' => 'required|integer'
            ]
        );

        $user = Auth::user();

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'skill' => $validated['skill'],
            'status' => $validated['status'],
            'entity_id' => $user->entity_id,
            'admin_user_id' => $user->id
        ];

        try {
            Vacancies::create($data);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'error saving job post'], 403);
        }
        return response()->json(['status' => true, 'message' => 'Job post created successfully'], 200);
    }

    public function updateJob(Request $request)
    {
        $response = Gate::inspect('updateJobs', Jobs::class);

        if (!$response->allowed()) {
            return response()->json(['status' => false, 'message' => $response->message()], 403);
        }

        $jobid = $request->input('jobid');

        $validated = $request->validate(
            [
                'title' => 'required|string',
                'description' => 'required|string',
                'skill' => 'string',
                'status' => 'required|integer'
            ]
        );

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'skill' => $validated['skill'],
            'status' => $validated['status']
        ];

        try {
            $update = Vacancies::find($jobid)->update($data);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'error saving job post'], 403);
        }

        return response()->json(['status' => true, 'message' => 'Job updated successfully'], 200);
    }

    public function getJob(Request $request)
    {
        $response = Gate::inspect('viewJobs', Jobs::class);

        if (!$response->allowed()) {
            return response()->json(['status' => false, 'message' => $response->message()], 403);
        }

        $validator = Validator::make($request->all(), [
            'job_id' => 'required|integer',
        ]);

        $validated = $validator->validated();

        $user = Auth::user();
        $job = Vacancies::where('id', $validated['job_id'])
            ->where('entity_id', $user->entity_id)
            ->get();

        return response()->json(['status' => true, 'data' => $job], 200);
    }
}
