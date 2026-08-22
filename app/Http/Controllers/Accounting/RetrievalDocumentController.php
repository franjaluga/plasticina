<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\VCDocuments\VCDocument;

class RetrievalDocumentController extends Controller
{
    public function show($id)
    {
        $document = VCDocument::with(['journal.entries.account', 'entity', 'documentType'])->findOrFail($id);

        return view('accounting.document_detail', compact('document'));
    }
}