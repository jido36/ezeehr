<?php

namespace App\Http\Services\Admin;

use App\Admin\Models\ActivityLog as ModelsActivityLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\ActivityLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;


class ActivityLogService
{

    public function log($activity, $object, $object_id, $admin_id)
    {
        try {
            $data = [
                'activity' => $activity,
                'object' => $object,
                'object_id' => $object_id,
                'admin_id' => $admin_id
            ];
            ActivityLog::create($data);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'Error creating activity log, kindly contact the site admin'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        // return response()->json(['status' => true, 'message' => 'Job post created successfully'], 200);
    }
}
