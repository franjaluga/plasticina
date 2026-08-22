<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Accounts\Account;
use App\Services\OwnerService;
use App\Services\ManualJournalService;
use Illuminate\Http\Request;
use Exception;

class ManualJournalController extends Controller
{
    protected ManualJournalService $manualJournalService;

    public function __construct(ManualJournalService $manualJournalService)
    {
        $this->manualJournalService = $manualJournalService;
    }

    public function create(OwnerService $ownerService)
    {
        $activeOwner = $ownerService->getActiveOwner();
        $accounts = Account::orderBy('code', 'asc')->get();
        $workingYear = session('working_year', date('Y'));

        return view('accounting.manual_journals_create', compact('accounts', 'workingYear'));
    }

    public function store(Request $request, OwnerService $ownerService)
    {
        $request->validate([
            'date' => 'required|date',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000',
            'glosa' => 'required|string|max:255',
            'entries' => 'required|array|min:2',
            'entries.*.account_code' => 'required|string',
            'entries.*.debit' => 'required|numeric|min:0',
            'entries.*.credit' => 'required|numeric|min:0',
        ]);

        $activeOwner = $ownerService->getActiveOwner();

        try {
            $this->manualJournalService->registerManualJournal(
                year: (int) $request->year,
                month: (int) $request->month,
                date: $request->date,
                description: $request->glosa,
                entries: $request->entries
            );
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['balance' => $e->getMessage()]);
        }
        
        return redirect()->route('accounting.reports.index')->with('success', 'Asiento contable manual creado exitosamente.');
    }
}