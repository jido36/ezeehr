<?php

namespace App\Http\Services\Candidates;

use App\Models\Documents;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class DocumentService
{

    public function uploadDocument(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'document' => 'required|file|mimetypes:application/pdf',
            'type' => 'required|string'
        ]);


        $validator->validated();

        $type = $request->input('type');

        $document = $request->file('document')->store($type);
        $applicant_id = Auth::id();

        try {
            $document_data = [
                'document_id' => $document,
                'type' => $request->input('type'),
                'name' => '',
                'applicant_id' => $applicant_id
            ];

            $return_document = Documents::create($document_data);

            return response()->json(['status' => true, 'message' => 'CV Uploaded Successfully', 'data' => $return_document], Response::HTTP_OK);
        } catch (\Exception $e) {
            Storage::delete([$document]);
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'Error occured'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'please select the CV to delete it', 'errors' => $validator->errors()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $validated = $validator->validated();

        try {
            Storage::delete($validated['document_id']);
            $document = Documents::where('document_id', $validated['document_id']);
            $document->delete();
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'Error deleting CV'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
