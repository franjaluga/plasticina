<?php

namespace App\Services;

use App\Models\VCDocuments\VCDocument;
use App\Services\JournalService;
use Exception;
use App\Models\Accounting\Journal;
use Illuminate\Database\Eloquent\Collection;

class DocumentAccountingService
{
    protected JournalService $journalService;

    public function __construct(JournalService $journalService)
    {
        $this->journalService = $journalService;
    }

    public function batchProcess(array $documentIds): array
    {
        $successCount = 0;
        $errorMessages = [];

        $accountMapping = [
            'net'     => ['account_code' => '110101', 'type' => 'debit'],
            'vat_rec' => ['account_code' => '110201', 'type' => 'debit'],
            'total'   => ['account_code' => '210101', 'type' => 'credit'],
        ];

        foreach ($documentIds as $id) {
            $document = VCDocument::find($id);

            try {
                $this->journalService->registerDocumentJournal($document, $accountMapping);
                $successCount++;
            } catch (Exception $e) {
                $errorMessages[] = "Doc ID {$id}: " . $e->getMessage();
            }
        }

        return [
            'success_count' => $successCount,
            'errors'        => $errorMessages,
        ];
    }

    public function getJournalBookRecords(): Collection
    {
        return Journal::with(['entries', 'document'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    }

    public function getPendingDocuments(): Collection
    {
        return VCDocument::doesntHave('journal')->get();
    }
}