<?php

namespace App\Http\Controllers\VCDocuments;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVCDocumentRequest;
use App\Models\VCDocuments\VCDocument; // Obligatorio importar el modelo
use App\Services\VCDocumentService;
use App\Services\JournalService; // Importamos el servicio de contabilidad
use Illuminate\Http\Request;

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

    public function pendingList()
    {
        $documents = VCDocument::doesntHave('journal')->get();

        return view('vc_documents.pending', compact('documents'));
    }

    public function batchContabilizar(Request $request, JournalService $journalService)
    {
        $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:vc_documents,id',
        ]);

        $successCount = 0;
        $errorMessages = [];

        $accountMapping = [
            'net'     => ['account_code' => '110101', 'type' => 'debit'],
            'vat_rec' => ['account_code' => '110201', 'type' => 'debit'],
            'total'   => ['account_code' => '210101', 'type' => 'credit'],
        ];

        foreach ($request->document_ids as $id) {
            $document = VCDocument::find($id);
            
            try {
                $journalService->registerDocumentJournal($document, $accountMapping);
                $successCount++;
            } catch (\Exception $e) {
                $errorMessages[] = "Doc ID {$id}: " . $e->getMessage();
            }
        }

        $redirect = back()->with('success', "Se contabilizaron exitosamente {$successCount} documentos.");

        if (!empty($errorMessages)) {
            $redirect->withErrors(['batch_errors' => implode(' | ', $errorMessages)]);
        }

        return $redirect;
    }

    public function journalBook()
    {
        $journals = \App\Models\Accounting\Journal::with(['entries', 'document'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return view('vc_documents.journal_book', compact('journals'));
    }
}