<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Accounts\Account;
use App\Models\Accounting\Journal;
use Illuminate\Http\Request;
use App\Services\TaxBalanceService;
use App\Services\JournalReportService;
use App\Services\JournalDetailService;
use App\Models\Masters\DocumentType;
use Illuminate\Support\Facades\DB;
use Exception;

class AccountingReportController extends Controller
{
    public function index()
    {
        $accounts = Account::orderBy('code', 'asc')->get();

        return view('accounting.index', compact('accounts'));
    }
    
    public function taxBalance(TaxBalanceService $balanceService)
    {
        $year = session('working_year', date('Y'));
        $balanceRows = $balanceService->generateBalance((int) $year);

        return view('accounting.tax_balance', compact('balanceRows', 'year'));
    }

    public function systemJournalsIndex(Request $request, JournalReportService $reportService)
    {
        $year = session('working_year', date('Y'));
        
        // Capturar todos los filtros enviados por GET
        $filters = $request->only([
            'entry_from', 'entry_to', 
            'date_from', 'date_to', 
            'folio_from', 'folio_to', 
            'rut', 'document_type_id', 'folio_ref'
        ]);

        $journals = $reportService->getSystemJournals((int) $year, $filters);
        
        // Obtener los tipos de documentos para el select del filtro
        $documentTypes = DocumentType::all();

        return view('accounting.system_journals', compact('journals', 'year', 'filters', 'documentTypes'));
    }

    public function showJournalDetail($id, JournalDetailService $detailService)
    {
        $journal = $detailService->getJournalDetails((int) $id);

        return view('accounting.journal_detail', compact('journal'));
    }

    public function analyticsIndex()
    {
        $accounts = Account::orderBy('code', 'asc')->get();

        return view('accounting.analytics', compact('accounts'));
    }

    public function editJournal($id)
    {
        $journal = Journal::with(['entries.account', 'document.entity', 'document.documentType'])->findOrFail($id);

        if (!$journal->vc_document_id) {
            return redirect()->route('accounting.system_journals')
                ->with('error', 'Este asiento no está asociado a un documento V/C de compra/venta.');
        }

        $accounts = Account::orderBy('code', 'asc')->get();

        return view('accounting.edit_journal', compact('journal', 'accounts'));
    }

    public function updateJournal(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'entries' => 'required|array|min:2',
            'entries.*.account_code' => 'required|string',
            'entries.*.debit' => 'required|numeric|min:0',
            'entries.*.credit' => 'required|numeric|min:0',
        ]);

        $journal = Journal::with(['entries', 'document'])->findOrFail($id);

        if (!$journal->vc_document_id) {
            return back()->with('error', 'Acción no permitida para este tipo de asiento.');
        }

        try {
            DB::transaction(function () use ($request, $journal) {
                $document = $journal->document;
                $tipo = strtoupper(trim($document->type_vc ?? 'C'));
                
                // El componente de devengo que no puede modificar su monto total
                $devengoComponent = 'total';
                $originalTotalAmount = (float) $document->total;

                $totalDebit = 0;
                $totalCredit = 0;
                $newEntriesData = [];

                foreach ($request->entries as $entryInput) {
                    $debit = (float) ($entryInput['debit'] ?? 0);
                    $credit = (float) ($entryInput['credit'] ?? 0);
                    $componentName = $entryInput['component_name'] ?? 'custom';
                    $accountCode = $entryInput['account_code'];

                    // Validación de seguridad para que la fila del devengo mantenga su monto inalterable
                    if ($componentName === $devengoComponent) {
                        $enteredAmount = ($tipo === 'V' || $tipo === 'VENTA') ? $debit : $credit;
                        
                        if (round($enteredAmount, 2) !== round($originalTotalAmount, 2)) {
                            throw new Exception("La línea del devengo (Proveedores/Clientes) no puede modificar su monto total ($originalTotalAmount).");
                        }
                    }

                    $totalDebit += $debit;
                    $totalCredit += $credit;

                    $newEntriesData[] = [
                        'account_code'   => $accountCode,
                        'component_name' => $componentName,
                        'debit'          => $debit,
                        'credit'         => $credit,
                    ];
                }

                // Validar la cuadratura exacta del asiento modificado
                if (round($totalDebit, 2) !== round($totalCredit, 2)) {
                    throw new Exception("El asiento modificado no está cuadrado. Total Debe: {$totalDebit}, Total Haber: {$totalCredit}");
                }

                $journal->update([
                    'date'         => $request->date,
                    'description'  => $request->description,
                    'total_debit'  => $totalDebit,
                    'total_credit' => $totalCredit,
                    'is_balanced'  => true,
                ]);

                // Reemplazar las líneas anteriores por las nuevas líneas filtradas/editadas
                $journal->entries()->delete();
                foreach ($newEntriesData as $entry) {
                    $journal->entries()->create($entry);
                }
            });

            return redirect()->route('accounting.system_journals')
                ->with('success', 'Asiento contable actualizado exitosamente.');

        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}