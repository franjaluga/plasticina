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
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->whereHas('document', function ($query) use ($activeOwner) {
                $query->where('owner_id', $activeOwner?->id);
            })
            ->whereHas('entries', function ($query) use ($accountCode) {
                $query->where('account_code', trim($accountCode));
            })
            ->orderBy('date', 'asc')
            ->get();
    }
}