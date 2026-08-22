<?php

namespace App\Services;

use App\Models\VCDocuments\VCDocument;
use App\Models\Accounting\Journal;
use Illuminate\Support\Facades\DB;
use Exception;

class JournalService
{
    public function registerDocumentJournal(VCDocument $document, array $accountMapping): Journal
    {
        // 1. Validación preventiva: Verificar si ya tiene un asiento contable
        if ($document->journal()->exists()) {
            throw new Exception("El documento con ID {$document->id} ya ha sido contabilizado previamente.");
        }

        return DB::transaction(function () use ($document, $accountMapping) {
            $entriesData = [];
            $totalDebit = 0;
            $totalCredit = 0;

            // 2. Componentes evaluables de la factura
            $components = [
                'net' => $document->net,
                'exempt' => $document->exempt,
                'vat_rec' => $document->vat_rec,
                'vat_no_rec' => $document->vat_no_rec,
                'plus_oth_tax' => $document->plus_oth_tax,
                'minus_oth_tax' => $document->minus_oth_tax,
                'total' => $document->total,
            ];

            foreach ($components as $componentName => $amount) {
                if ($amount && $amount != 0) {
                    if (!isset($accountMapping[$componentName])) {
                        continue; 
                    }

                    $debit = $accountMapping[$componentName]['type'] === 'debit' ? abs($amount) : 0;
                    $credit = $accountMapping[$componentName]['type'] === 'credit' ? abs($amount) : 0;

                    $totalDebit += $debit;
                    $totalCredit += $credit;

                    $entriesData[] = [
                        'account_code' => $accountMapping[$componentName]['account_code'],
                        'component_name' => $componentName,
                        'debit' => $debit,
                        'credit' => $credit,
                    ];
                }
            }

            // 3. Revisar cuadratura (Debe == Haber)
            $isBalanced = round($totalDebit, 2) === round($totalCredit, 2);

            if (!$isBalanced) {
                throw new Exception("El asiento contable no está cuadrado. Total Debe: {$totalDebit}, Total Haber: {$totalCredit}");
            }

            // 4. Crear cabecera
            $journal = Journal::create([
                'vc_document_id' => $document->id,
                'date' => $document->date,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'is_balanced' => $isBalanced,
            ]);

            // 5. Crear líneas de detalle
            foreach ($entriesData as $entry) {
                $journal->entries()->create($entry);
            }

            return $journal;
        });
    }
}