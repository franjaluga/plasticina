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

        foreach ($documentIds as $id) {
            $document = VCDocument::find($id);

            if (!$document) {
                $errorMessages[] = "Doc ID {$id}: No encontrado.";
                continue;
            }

            try {
                // Nombre corregido para que coincida con el método de abajo
                $accountMapping = $this->getAccountMapping($document);

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

    /**
     * Define el mapeo de cuentas contables dependiendo si es Compra (C) o Venta (V).
     */
    protected function getAccountMapping(VCDocument $document): array
    {
        // Limpiamos espacios y pasamos a mayúsculas
        $tipo = strtoupper(trim($document->type_vc ?? 'C')); 

        // Si es Venta (puedes ajustar si en tu BD se guarda diferente, ej: 'VENTA')
        if ($tipo === 'V' || $tipo === 'VENTA') {
            return [
                'net'     => ['account_code' => '410101', 'type' => 'credit'], // Ventas (Haber)
                'vat_rec' => ['account_code' => '210201', 'type' => 'credit'], // IVA Débito (Haber)
                'total'   => ['account_code' => '110102', 'type' => 'debit'],  // Clientes (Debe)
            ];
        }

        // Por defecto Compras ('C')
        return [
            'net'     => ['account_code' => '110101', 'type' => 'debit'],  // Compras (Debe)
            'vat_rec' => ['account_code' => '110201', 'type' => 'debit'],  // IVA Crédito (Debe)
            'total'   => ['account_code' => '210101', 'type' => 'credit'], // Proveedores (Haber)
        ];
    }

    public function getJournalBookRecords(int $year): Collection
    {
        $activeOwner = app(OwnerService::class)->getActiveOwner();

        return Journal::with(['entries.account', 'document'])
            ->whereHas('document', function ($query) use ($year, $activeOwner) {
                $query->where('year_register', $year)
                      ->where('owner_id', $activeOwner?->id);
            })
            ->orderBy('date', 'desc')
            ->get();
    }

    public function getPendingDocuments(): Collection
    {
        $activeOwner = app(OwnerService::class)->getActiveOwner();

        return VCDocument::where('owner_id', $activeOwner?->id)
            ->doesntHave('journal')
            ->orderBy('year_register', 'desc')
            ->orderBy('month_register', 'desc')
            ->get();
    }
}