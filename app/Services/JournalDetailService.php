<?php

namespace App\Services;

use App\Models\Accounting\Journal;

class JournalDetailService
{
    public function getJournalDetails(int $journalId): Journal
    {
        // Cargamos el asiento con sus líneas de detalle, la cuenta contable de cada línea,
        // y opcionalmente el documento V/C asociado si lo tuviera.
        return Journal::with(['entries.account', 'document.entity', 'document.documentType'])
            ->findOrFail($journalId);
    }
}