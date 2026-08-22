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

    public function batchProcess(array $documentIds, ?string $customNetAccount = null): array
    {
        $successCount = 0;
        $errorMessages = [];

        foreach ($documentIds as $id) {
            $document = VCDocument::find($id);

            if (!$document) {
                $errorMessages[] = "Doc ID {$id}: No encontrado.";
                continue;
            }

            try {
                $accountMapping = $this->getAccountMapping($document, $customNetAccount);
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

    protected function getAccountMapping(VCDocument $document, ?string $customNetAccount = null): array
    {
        $tipo = strtoupper(trim($document->type_vc ?? 'C')); 

        $netAccountCode = !empty($customNetAccount) ? $customNetAccount : ($tipo === 'V' || $tipo === 'VENTA' ? '410101' : '110101');

        if ($tipo === 'V' || $tipo === 'VENTA') {
            return [
                'net'     => ['account_code' => $netAccountCode, 'type' => 'credit'],
                'vat_rec' => ['account_code' => '210201', 'type' => 'credit'],
                'total'   => ['account_code' => '110102', 'type' => 'debit'],
            ];
        }

        return [
            'net'     => ['account_code' => $netAccountCode, 'type' => 'debit'],
            'vat_rec' => ['account_code' => '110201', 'type' => 'debit'],
            'total'   => ['account_code' => '210101', 'type' => 'credit'],
        ];
    }

    public function getJournalBookRecords(?int $year = null): Collection
    {
        $year = $year ?? session('working_year', date('Y'));
        $activeOwner = app(OwnerService::class)->getActiveOwner();

        return Journal::with(['entries.account', 'document'])
            ->where('owner_id', $activeOwner?->id)
            ->where('year', $year)
            ->orderBy('date', 'asc')         // Orden ascendente por fecha
            ->orderBy('entry_number', 'asc') // Orden ascendente por correlativo de asiento
            ->get();
    }

    public function getPendingDocuments(): Collection
    {
        $activeOwner = app(OwnerService::class)->getActiveOwner();
        $workingYear = session('working_year', date('Y'));

        return VCDocument::where('owner_id', $activeOwner?->id)
            ->where('year_register', $workingYear)
            ->doesntHave('journal')
            ->orderBy('month_register', 'asc')
            ->orderBy('date', 'asc')
            ->get();
    }
}