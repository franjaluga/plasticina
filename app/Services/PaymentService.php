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

        // Sumamos directamente los asientos de pago vinculados a este documento por la nueva columna
        $paid = DB::table('journal_entries')
            ->join('journals', 'journals.id', '=', 'journal_entries.journal_id')
            ->where('journals.owner_id', $ownerId)
            ->where('journals.ref_doc_payed', $document->id)
            ->where('journal_entries.account_code', $accountCodeToCheck)
            ->sum($tipo === 'V' || $tipo === 'VENTA' ? 'journal_entries.credit' : 'journal_entries.debit');

        return (float) $paid;
    }

    public function processPayment(int $documentId, float $amount, string $date, string $bankAccountCode): Journal
    {
        $activeOwner = $this->ownerService->getActiveOwner();
        if (!$activeOwner) {
            throw new Exception("No hay un Owner activo.");
        }

        return DB::transaction(function () use ($documentId, $amount, $date, $bankAccountCode, $activeOwner) {
            $document = VCDocument::findOrFail($documentId);
            $tipo = strtoupper(trim($document->type_vc ?? 'C'));

            $totalDoc = (float) $document->total;
            $paidAmount = $this->calculatePaidAmount($document, $activeOwner->id);
            $balance = round($totalDoc - $paidAmount, 2);

            if (round($amount, 2) > $balance) {
                throw new Exception("El monto ingresado ({$amount}) excede el saldo pendiente del documento ({$balance}).");
            }

            $year = date('Y', strtotime($date));
            $month = date('n', strtotime($date));
            
            if ($tipo === 'V' || $tipo === 'VENTA') {
                $entries = [
                    [
                        'account_code'   => $bankAccountCode,
                        'component_name' => 'payment',
                        'debit'          => $amount,
                        'credit'         => 0,
                    ],
                    [
                        'account_code'   => '110102', // Clientes
                        'component_name' => 'payment',
                        'debit'          => 0,
                        'credit'         => $amount,
                    ]
                ];
            } else {
                $entries = [
                    [
                        'account_code'   => '210101', // Proveedores
                        'component_name' => 'payment',
                        'debit'          => $amount,
                        'credit'         => 0,
                    ],
                    [
                        'account_code'   => $bankAccountCode,
                        'component_name' => 'payment',
                        'debit'          => 0,
                        'credit'         => $amount,
                    ]
                ];
            }

            $lastEntryNumber = Journal::where('owner_id', $activeOwner->id)
                ->where('year', $year)
                ->lockForUpdate()
                ->max('entry_number');
            $nextEntryNumber = ($lastEntryNumber ?? 0) + 1;

            // Creamos el asiento guardando explícitamente el documento pagado en 'ref_doc_payed'
            $journal = Journal::create([
                'vc_document_id' => null, 
                'ref_doc_payed'  => $document->id, // <-- Aquí queda la trazabilidad oficial
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