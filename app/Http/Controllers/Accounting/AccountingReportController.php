<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Accounts\Account;
use Illuminate\Http\Request;
use App\Services\TaxBalanceService;
use App\Services\JournalReportService;
use App\Services\JournalDetailService;
use App\Models\Masters\DocumentType;

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
}