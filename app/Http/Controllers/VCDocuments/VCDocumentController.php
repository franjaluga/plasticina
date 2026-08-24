<?php

namespace App\Http\Controllers\VCDocuments;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVCDocumentRequest;
use App\Models\VCDocuments\VCDocument;
use App\Services\VCDocumentService;
use App\Services\JournalService;
use Illuminate\Http\Request;
use App\Services\DocumentAccountingService;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Services\BooksToCsv;
use App\Models\Accounts\Account;

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
            dd($e->getMessage(), $e->getFile(), $e->getLine());
        }
    }

    public function pendingList(DocumentAccountingService $accountingService)
    {
        $documents = $accountingService->getPendingDocuments();
        
        $accounts = Account::orderBy('code')->get();

        return view('vc_documents.pending', compact('documents', 'accounts'));
    }

    public function batchContabilizar(Request $request, DocumentAccountingService $accountingService)
    {
        $request->validate([
            'document_ids' => 'required|array',
            'custom_net_account' => 'required|string|max:20',
        ], [
            'custom_net_account.required' => 'Debe seleccionar o indicar una cuenta contable para el Neto.',
        ]);

        $documentIds = $request->input('document_ids');
        $customNetAccount = $request->input('custom_net_account');

        $result = $accountingService->batchProcess($documentIds, $customNetAccount);

        return back()->with('success', "Se procesaron correctamente {$result['success_count']} documentos.");
    }

    public function journalBook(DocumentAccountingService $accountingService)
    {
        $year = session('working_year', date('Y'));

        $journals = $accountingService->getJournalBookRecords((int) $year);

        return view('vc_documents.journal_book', compact('journals', 'year'));
    }

    public function exportCsv(BooksToCsv $csvService): StreamedResponse
    {
        return $csvService->export();
    }
}