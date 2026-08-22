<?php

namespace App\Services;

use App\Models\Accounting\Journal;
use Illuminate\Database\Eloquent\Collection;

class LedgerService
{
    public function getLedgerRecords(string $accountCode, int $month, int $year): Collection
    {
        $activeOwner = app(OwnerService::class)->getActiveOwner();

        return Journal::with(['entries.account', 'document.entity'])
            ->where('owner_id', $activeOwner?->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->whereHas('entries', function ($query) use ($accountCode) {
                $query->where('account_code', trim($accountCode));
            })
            ->orderBy('date', 'asc')
            ->orderBy('entry_number', 'asc')
            ->get();
    }
}