<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Services\TaxBalanceService;

class AccountingReportController extends Controller
{
    public function taxBalance(TaxBalanceService $balanceService)
    {
        $year = session('working_year', date('Y'));
        $balanceRows = $balanceService->generateBalance((int) $year);

        return view('accounting.tax_balance', compact('balanceRows', 'year'));
    }
}