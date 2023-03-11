<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Models\Admin\Jobs;
use Illuminate\Support\Facades\Log;

class JobsController extends Controller
{
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

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'skill' => $validated['skill'],
            'status' => $validated['status']
        ];

        try {
            Jobs::create($data);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'error saving job post'], 403);
        }
        return response()->json(['status' => true, 'message' => 'Job post created successfully'], 200);
    }
}
