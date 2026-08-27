<?php

namespace App\Services;

use App\Models\Accounting\Journal;
use App\Models\VCDocuments\VCDocument;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentService
{
    protected OwnerService $ownerService;

    public function __construct(OwnerService $ownerService)
    {
        $this->ownerService = $ownerService;
    }

    /**
     * Obtiene documentos de compras y ventas que tienen saldo pendiente distinto de cero.
     */
    public function getPendingBalanceDocuments(?int $year = null): Collection
    {
        $workingYear = $year ?? session('working_year', date('Y'));
        $activeOwner = $this->ownerService->getActiveOwner();

        if (!$activeOwner) {
            return new Collection();
        }

        $documents = VCDocument::where('owner_id', $activeOwner->id)
            ->where('year_register', $workingYear)
            ->has('journal')
            ->get();

        return $documents->filter(function ($doc) use ($activeOwner) {
            $totalDoc = (float) $doc->total;
            
            $paidAmount = $this->calculatePaidAmount($doc, $activeOwner->id);
            $balance = $totalDoc - $paidAmount;

            $doc->calculated_paid = $paidAmount;
            $doc->calculated_balance = round($balance, 2);

            return round($balance, 2) != 0;
        });
    }

    protected function calculatePaidAmount(VCDocument $document, int $ownerId): float
    {
        $tipo = strtoupper(trim($document->type_vc ?? 'C'));
        $accountCodeToCheck = ($tipo === 'V' || $tipo === 'VENTA') ? '110102' : '210101';
        $componentTag = 'payment_doc_' . $document->id;

        // Sumamos estrictamente los abonos que tengan la etiqueta asociada a ESTE documento ID
        $paid = DB::table('journal_entries')
            ->join('journals', 'journals.id', '=', 'journal_entries.journal_id')
            ->where('journals.owner_id', $ownerId)
            ->whereNull('journals.vc_document_id')
            ->where('journal_entries.account_code', $accountCodeToCheck)
            ->where('journal_entries.component_name', $componentTag)
            ->sum($tipo === 'V' || $tipo === 'VENTA' ? 'journal_entries.credit' : 'journal_entries.debit');

        return (float) $paid;
    }

    /**
     * Procesa el pago (Compra -> Proveedor) o cobro (Venta -> Cliente)
     */
    public function processPayment(int $documentId, float $amount, string $date, string $bankAccountCode): Journal
    {
        $activeOwner = $this->ownerService->getActiveOwner();
        if (!$activeOwner) {
            throw new Exception("No hay un Owner activo.");
        }

        return DB::transaction(function () use ($documentId, $amount, $date, $bankAccountCode, $activeOwner) {
            $document = VCDocument::findOrFail($documentId);
            $tipo = strtoupper(trim($document->type_vc ?? 'C'));

            // --- VALIDACIÓN DE SALDO MÁXIMO ---
            $totalDoc = (float) $document->total;
            $paidAmount = $this->calculatePaidAmount($document, $activeOwner->id);
            $balance = round($totalDoc - $paidAmount, 2);

            if (round($amount, 2) > $balance) {
                throw new Exception("El monto ingresado ({$amount}) excede el saldo pendiente del documento ({$balance}).");
            }
            // ---------------------------------

            $year = date('Y', strtotime($date));
            $month = date('n', strtotime($date));
            
            // Etiqueta única para rastrear este pago exclusivamente a este documento
            $componentTag = 'payment_doc_' . $document->id;
            
            if ($tipo === 'V' || $tipo === 'VENTA') {
                // Cobro de Venta: Banco (Debe) / Clientes (Haber)
                $entries = [
                    [
                        'account_code'   => $bankAccountCode,
                        'component_name' => $componentTag,
                        'debit'          => $amount,
                        'credit'         => 0,
                    ],
                    [
                        'account_code'   => '110102', // Clientes
                        'component_name' => $componentTag,
                        'debit'          => 0,
                        'credit'         => $amount,
                    ]
                ];
            } else {
                // Pago de Compra: Proveedores (Debe) / Banco (Haber)
                $entries = [
                    [
                        'account_code'   => '210101', // Proveedores
                        'component_name' => $componentTag,
                        'debit'          => $amount,
                        'credit'         => 0,
                    ],
                    [
                        'account_code'   => $bankAccountCode,
                        'component_name' => $componentTag,
                        'debit'          => 0,
                        'credit'         => $amount,
                    ]
                ];
            }

            // Calcular número de asiento correlativo
            $lastEntryNumber = Journal::where('owner_id', $activeOwner->id)
                ->where('year', $year)
                ->lockForUpdate()
                ->max('entry_number');
            $nextEntryNumber = ($lastEntryNumber ?? 0) + 1;

            // Crear el asiento manual (vc_document_id = null)
            $journal = Journal::create([
                'vc_document_id' => null, 
                'owner_id'       => $activeOwner->id,
                'year'           => (int) $year,
                'month'          => (int) $month,
                'date'           => $date,
                'entry_number'   => $nextEntryNumber,
                'total_debit'    => $amount,
                'total_credit'   => $amount,
                'is_balanced'    => true,
            ]);

            foreach ($entries as $entry) {
                $journal->entries()->create($entry);
            }

            return $journal;
        });
    }
}