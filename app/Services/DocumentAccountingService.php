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

    public function batchProcess(array $documentIds, ?string $customNetAccount = null, ?string $glosa = null): array
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
                
                $this->journalService->registerDocumentJournal($document, $accountMapping, $glosa);
                
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
        if (empty($customNetAccount)) {
            throw new Exception("No se ha especificado la cuenta contable para el Neto.");
        }

        $tipo = strtoupper(trim($document->type_vc ?? 'C')); 

        if ($tipo === 'V' || $tipo === 'VENTA') {
            return [
                'net'           => ['account_code' => $customNetAccount, 'type' => 'credit'],
                'exempt'        => ['account_code' => '4010110', 'type' => 'credit'],
                'vat_rec'       => ['account_code' => '1010802', 'type' => 'credit'],
                'vat_no_rec'    => ['account_code' => '4011021', 'type' => 'credit'],
                'plus_oth_tax'  => ['account_code' => '1010804', 'type' => 'credit'],
                'minus_oth_tax' => ['account_code' => '1010804', 'type' => 'debit'],
                'total'         => ['account_code' => '1010401', 'type' => 'debit'],
            ];
        }

        // Compras (C)
        return [
            'net'           => ['account_code' => $customNetAccount, 'type' => 'debit'],
            'exempt'        => ['account_code' => '40109', 'type' => 'debit'],
            'vat_rec'       => ['account_code' => '1010802', 'type' => 'debit'],
            'vat_no_rec'    => ['account_code' => '4011021', 'type' => 'debit'],
            'plus_oth_tax'  => ['account_code' => '1010804', 'type' => 'debit'],
            'minus_oth_tax' => ['account_code' => '1010804', 'type' => 'credit'],
            'total'         => ['account_code' => '2010201', 'type' => 'credit'],
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

    public function getPendingDocuments(array $filters = []): Collection
    {
        $activeOwner = app(OwnerService::class)->getActiveOwner();
        $workingYear = session('working_year', date('Y'));

        $query = VCDocument::with(['entity', 'documentType'])
            ->where('owner_id', $activeOwner?->id)
            ->where('year_register', $workingYear)
            ->doesntHave('journal');

        // 1. Filtro por RUT (Entidad)
        if (!empty($filters['rut'])) {
            $query->whereHas('entity', fn($e) => $e->where('rut', 'LIKE', '%' . $filters['rut'] . '%'));
        }

        // 2. Filtro por Tipo de Documento
        if (!empty($filters['document_type_id'])) {
            $query->where('document_type_id', $filters['document_type_id']);
        }

        // 3. Filtro por Fecha específica o Rango
        if (!empty($filters['date'])) {
            $query->where('date', $filters['date']);
        }

        // 4. Filtro por Folio
        if (!empty($filters['folio'])) {
            $query->where('folio', $filters['folio']);
        }

        return $query->orderBy('month_register', 'asc')
            ->orderBy('date', 'asc')
            ->get();
    }
}