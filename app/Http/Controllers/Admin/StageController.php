<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\Admin\StageService;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\StageRequest;

class StageController extends Controller
{
    private $stageService;

    public function __construct(StageService $stageService)
    {
        $this->stageService = $stageService;
    }

    public function index()
    {
    }

    public function store(StageRequest $request)
    {

        $this->stageService->store($request);

        return response()->json(['status' => true, 'message' => 'Stage created'], 200);
    }

    public function update(StageRequest $request)
    {
        $this->stageService->update($request);

        return response()->json(['status' => true, 'message' => 'Stage updated'], 200);
    }
}
