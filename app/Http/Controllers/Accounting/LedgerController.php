<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Accounts\Account;
use App\Services\LedgerService;
use App\Services\OwnerService;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    protected LedgerService $ledgerService;

    public function __construct(LedgerService $ledgerService)
    {
        $this->ledgerService = $ledgerService;
    }

    public function index(Request $request, OwnerService $ownerService)
    {
        // Reemplaza la línea anterior de accounts por esta:
        $accounts = Account::getActiveOwnerAccounts();

        $accountCode = $request->input('account_code');
        $month = (int) $request->input('month', date('n'));
        $year = (int) $request->input('year', session('working_year', date('Y')));

        $journals = collect();
        if (!empty($accountCode)) {
            $journals = $this->ledgerService->getLedgerRecords($accountCode, $month, $year);
        }

        return view('accounting.ledger_results', compact('journals', 'accounts', 'accountCode', 'month', 'year'));
    }
}