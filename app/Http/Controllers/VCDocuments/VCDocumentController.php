<?php

namespace App\Http\Controllers\VCDocuments;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVCDocumentRequest;
use App\Models\VCDocuments\VCDocument;
use App\Services\VCDocumentService;
use App\Services\JournalService;
use Illuminate\Http\Request;
use App\Services\DocumentAccountingService;

class VCDocumentController extends Controller
{
    public function create()
    {
        return view('vc_documents.create');
    }

    public function checkEntity($rut, VCDocumentService $service)
    {
        $entity = $service->getEntityByRut($rut);

        return response()->json([
            'exists' => (bool) $entity,
            'name'   => $entity->name ?? null,
        ]);
    }

    public function checkDocumentType($doctype, VCDocumentService $service)
    {
        $docType = $service->getDocumentTypeByDoctype($doctype);

        return response()->json([
            'exists' => (bool) $docType,
            'name'   => $docType->name ?? null,
        ]);
    }

    public function store(StoreVCDocumentRequest $request, VCDocumentService $service)
    {
        try {
            $service->persistDocument($request->validated());

            return redirect()
                ->route('vc_documents.create')
                ->with('success', 'Documento V/C guardado exitosamente.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['duplicate' => $e->getMessage()]);
        }
    }

    public function csvImport(Request $request, VCDocumentService $service)
    {
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        try {
            $rowsProcessed = $service->importCsv($request->file('csv_file'));

            return redirect()
                ->route('vc_documents.create')
                ->with('success', "¡CSV importado correctamente! Se ingresaron {$rowsProcessed} documentos.");
                
        } catch (\Exception $e) {
            return back()->withErrors(['csv_file' => $e->getMessage()]);
        }
    }

    public function pendingList(DocumentAccountingService $accountingService)
    {
        $documents = $accountingService->getPendingDocuments();

        return view('vc_documents.pending', compact('documents'));
    }

    public function batchContabilizar(Request $request, DocumentAccountingService $accountingService)
    {
        $request->validate([
            'document_ids'   => 'required|array',
            'document_ids.*' => 'exists:vc_documents,id',
        ]);

        $result = $accountingService->batchProcess($request->input('document_ids'));

        $redirect = back()->with('success', "Se contabilizaron exitosamente {$result['success_count']} documentos.");

        if (!empty($result['errors'])) {
            $redirect->withErrors(['batch_errors' => implode(' | ', $result['errors'])]);
        }

        return $redirect;
    }

    public function journalBook(DocumentAccountingService $accountingService)
    {
        $journals = $accountingService->getJournalBookRecords();

        return view('vc_documents.journal_book', compact('journals'));
    }
}