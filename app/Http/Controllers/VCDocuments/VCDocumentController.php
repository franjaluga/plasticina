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
use App\Services\OwnerService;

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

    public function pendingList(Request $request, DocumentAccountingService $accountingService)
    {
        $filters = $request->only(['rut', 'document_type_id', 'date', 'folio']);
        
        $documents = $accountingService->getPendingDocuments($filters);
        
        $accounts = Account::getActiveOwnerAccounts();
        $documentTypes = \App\Models\Masters\DocumentType::all();

        return view('vc_documents.pending', compact('documents', 'accounts', 'documentTypes', 'filters'));
    }

    public function batchContabilizar(Request $request, DocumentAccountingService $accountingService)
    {
        $request->validate([
            'document_ids' => 'required|array',
            'custom_net_account' => 'required|string|max:20',
            'glosa' => 'required|string|max:255', // Validamos que se incluya la glosa
        ], [
            'custom_net_account.required' => 'Debe seleccionar o indicar una cuenta contable para el Neto.',
            'glosa.required' => 'Debe ingresar una glosa o descripción general para los asientos.',
        ]);

        $documentIds = $request->input('document_ids');
        $customNetAccount = $request->input('custom_net_account');
        $glosa = $request->input('glosa'); // Capturamos la glosa ingresada

        // Pasamos la glosa al servicio de proceso por lotes
        $result = $accountingService->batchProcess($documentIds, $customNetAccount, $glosa);

        return back()->with('success', "Se procesaron correctamente {$result['success_count']} documentos.");
    }

    public function journalBook(\Illuminate\Http\Request $request)
    {
        $activeOwner = app(\App\Services\OwnerService::class)->getActiveOwner();
        $workingYear = session('working_year', date('Y'));

        // Obtener filtros de fecha si se enviaron desde el formulario de rango
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // Consulta unificada: Obtenemos todos los asientos (journals) del owner activo y año de trabajo
        $query = \App\Models\Accounting\Journal::with(['document.documentType', 'document.entity', 'entries.account', 'paidDocument'])
            ->where('year', $workingYear);

        if ($activeOwner) {
            $query->where('owner_id', $activeOwner->id);
        }

        // Aplicar filtro por rango de fechas si existe
        if ($dateFrom && $dateTo) {
            $query->whereBetween('date', [$dateFrom, $dateTo]);
        }

        $journals = $query->orderBy('date', 'asc')
                          ->orderBy('entry_number', 'asc')
                          ->get();

        // Si se solicitó exportar a CSV
        if ($request->routeIs('*.export_csv') || $request->has('export') && $request->input('export') === 'csv') {
            $filename = 'libro_diario_' . $workingYear . '.csv';
            
            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function() use ($journals) {
                $file = fopen('php://output', 'w');
                // Forzar codificación UTF-8 para Excel
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // Cabeceras del CSV
                fputcsv($file, ['N° Asiento', 'Fecha', 'Glosa / Descripción', 'Código Cuenta', 'Nombre Cuenta', 'Debe', 'Haber'], ';');

                foreach ($journals as $journal) {
                    $entryNumber = $journal->entry_number;
                    $date = $journal->date;
                    $glosa = $journal->description ?? 'Sin descripción';

                    foreach ($journal->entries as $entry) {
                        fputcsv($file, [
                            $entryNumber,
                            $date,
                            $glosa,
                            $entry->account_code,
                            $entry->account->name ?? 'Cuenta no encontrada',
                            $entry->debit,
                            $entry->credit
                        ], ';');
                    }
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Retornar la vista del Libro Diario con los asientos unificados (V/C + Manuales)
        return view('vc_documents.journal_book', compact('journals', 'workingYear', 'dateFrom', 'dateTo'));
    }

    public function exportCsv(BooksToCsv $csvService): StreamedResponse
    {
        return $csvService->export();
    }
}