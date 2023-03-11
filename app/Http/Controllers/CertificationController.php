<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use App\Models\Certifications;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CertificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'organisation' => 'required|string',
            'issue_date' => 'required|date_format:Y-m',
            'expiry_date' => 'nullable|date_format:Y-m',
        ]);

        $validated = $validator->validated();

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Error validating input', 'errors' => $validator->errors()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $data = [
            'name' => $validated['name'],
            'organisation' => $validated['organisation'],
            // 'issue_date' => '01-' . $validated['issue_date'] . ' 00:00:00',
            'issue_date' => $validated['issue_date'] . '-01',
            // 'expiry_date' => '01-' . $validated['expiry_date'] . ' 00:00:00',
            'expiry_date' => is_null($validated['expiry_date']) ? null : $validated['expiry_date'] . '-01',
            'user_id' => Auth::id()
        ];

        try {
            Certifications::create($data);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'Error Creating the experience', 'errors' => $validator->errors()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['status' => true, 'message' => 'Certification created', 'data' => $request->all()], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
