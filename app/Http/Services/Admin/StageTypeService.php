<?php

namespace App\Http\Services\Admin;

use App\Models\StageType;
use Illuminate\Support\Facades\Log;

class StageTypeService
{

    public function store($request)
    {
        $data = [
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'default' => $request->input('default'),
        ];
        try {
            StageType::create($data);
            return true;
        } catch (\Exception $e) {
            abort(422, "Error creating data");
            Log::error($e);
        }
    }

    public function update($request)
    {

        $stage = StageType::find($request->input('id'));

        try {
            $stage->update($request);
        } catch (\Exception $e) {
            abort(422, "Error creating data");
            Log::error($e);
        }
    }
}
