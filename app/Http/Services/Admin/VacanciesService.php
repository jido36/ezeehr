<?php

namespace App\Http\Services\Admin;

use Exception;
use App\Models\Admin\Vacancies;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VacanciesService
{

    public function listAllVacancies()
    {
        try {
            $user = Auth::user();
            return Vacancies::where('entity_id', $user->entity_id)->paginate(20);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'Error fetching jobs please contact admin'], 403);
        }
    }

    public function createJobs($request)
    {

        $user = Auth::user();

        $data = [
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'skill' => $request->input('skill'),
            'status' => $request->input('status'),
            'entity_id' => $user->entity_id,
            'admin_user_id' => $user->id,
            'stage_type_id' => $request->input('stage_type_id')
        ];

        try {
            Vacancies::create($data);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'error saving job post'], 403);
        }
        return response()->json(['status' => true, 'message' => 'Job post created successfully'], 200);
    }

    public function updateJob($request)
    {

        $jobid = $request->input('jobid');

        $data = [
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'skill' => $request->input('skill'),
            'status' => $request->input('status')
        ];

        try {
            $update = Vacancies::find($jobid)->update($data);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'error saving job post'], 403);
        }

        return response()->json(['status' => true, 'message' => 'Job updated successfully'], 200);
    }

    public function getJob($job_id)
    {
        // $validator = Validator::make($request->all(), [
        //     'job_id' => 'required|integer',
        // ]);

        // $validated = $validator->validated();

        $user = Auth::user();
        $job = Vacancies::where('id', $job_id)
            ->where('entity_id', $user->entity_id)
            ->get();

        return response()->json(['status' => true, 'data' => $job], 200);
    }
}
