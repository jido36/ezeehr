<?php

namespace App\Http\Services\Admin;

use App\Models\Stage;
use Illuminate\Support\Facades\Log;


class StageService
{


    public function store($request)
    {
        $data = [
            "title" => $request->input('title'),
            "description" => $request->input('description'),
            "order" => $request->input('order'),
            "stage_type_id" => $request->input('stage_type_id'),
        ];

        try {
            Stage::create($data);
            return true;
        } catch (\Exception $e) {
            abort(422, "Error creating data");
            Log::error($e);
        }
    }

    public function update($request)
    {

        $stage = Stage::find($request->input('id'));

        try {
            $stage->update($request);
        } catch (\Exception $e) {
            abort(422, "Error creating data");
            Log::error($e);
        }
    }
}
