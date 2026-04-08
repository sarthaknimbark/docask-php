<?php

namespace App\Http\Controllers;

use App\Services\DocumentService;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function __construct(private DocumentService $documentService)
    {
    }

    public function upload(Request $request)
    {
        $request->validate([
            // Accept only PDF up to 10MB
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);

        $result = $this->documentService->processPdf($request->file('file'));

        return response()->json([
            'message' => 'PDF uploaded and indexed successfully.',
            'data' => $result,
        ]);
    }
}
