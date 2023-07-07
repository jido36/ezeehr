<?php

namespace App\Http\Services\Candidates;

use Illuminate\Http\Response;
use App\Models\Certifications;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CertificationService
{
    public function store($request)
    {
        $data = [
            'name' => $request->input('name'),
            'organisation' => $request->input('organisation'),
            // 'issue_date' => '01-' . $validated['issue_date'] . ' 00:00:00',
            'issue_date' => $request->input('issue_date') . '-01',
            // 'expiry_date' => '01-' . $validated['expiry_date'] . ' 00:00:00',
            'expiry_date' => is_null($request->input('expiry_date')) ? null : $request->input('expiry_date') . '-01',
            'user_id' => Auth::id()
        ];

        try {
            Certifications::create($data);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'Error Creating the experience'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
