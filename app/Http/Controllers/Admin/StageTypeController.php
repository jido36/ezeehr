<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StageTypeRequest;
use App\Http\Services\Admin\StageTypeService;

class StageTypeController extends Controller
{
    private $stageTypeService;

    public function __construct(StageTypeService $stageTypeService)
    {
        $this->stageTypeService = $stageTypeService;
    }

    public function index()
    {
    }

    public function store(StageTypeRequest $request)
    {

        $this->stageTypeService->store($request);

        return response()->json(['status' => true, 'message' => 'Stage created'], 200);
    }

    public function update(StageTypeRequest $request)
    {
        $this->stageTypeService->update($request);

        return response()->json(['status' => true, 'message' => 'Stage updated'], 200);
    }
}
