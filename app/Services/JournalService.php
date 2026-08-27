<?php

namespace App\Services;

use App\Models\VCDocuments\VCDocument;
use App\Models\Accounting\Journal;
use Illuminate\Support\Facades\DB;
use Exception;

class JournalService
{
    public function registerDocumentJournal(VCDocument $document, array $accountMapping, ?string $glosa = null): Journal
    {
        // 1. Validación preventiva: Verificar si ya tiene un asiento contable
        if ($document->journal()->exists()) {
            throw new Exception("El documento con ID {$document->id} ya ha sido contabilizado previamente.");
        }

        return DB::transaction(function () use ($document, $accountMapping, $glosa) {
            
            $ownerId = $document->owner_id;
            $year = $document->year_register;

            if (!$ownerId) {
                throw new Exception("El documento no tiene un Owner asociado, no se puede generar el asiento contable.");
            }

            // 2. Calcular el siguiente número correlativo de asiento para ESTE owner y ESTE año.
            // Usamos lockForUpdate() para bloquear la lectura y evitar duplicados en procesos masivos/concurridos.
            $lastEntryNumber = Journal::where('owner_id', $ownerId)
                ->where('year', $year)
                ->lockForUpdate()
                ->max('entry_number');

            $nextEntryNumber = ($lastEntryNumber ?? 0) + 1;

            $entriesData = [];
            $totalDebit = 0;
            $totalCredit = 0;

            // 3. Componentes evaluables de la factura
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

            // 4. Revisar cuadratura (Debe == Haber)
            $isBalanced = round($totalDebit, 2) === round($totalCredit, 2);

            if (!$isBalanced) {
                throw new Exception("El asiento contable no está cuadrado. Total Debe: {$totalDebit}, Total Haber: {$totalCredit}");
            }

            // 5. Crear cabecera incluyendo el Owner, el Año, el Correlativo y la Glosa (description)
            $journal = Journal::create([
                'vc_document_id' => $document->id,
                'owner_id'       => $ownerId,
                'year'           => $year,
                'entry_number'   => $nextEntryNumber,
                'date'           => $document->date,
                'description'    => $glosa ?: ('Contabilización documento folio ' . $document->folio),
                'total_debit'    => $totalDebit,
                'total_credit'   => $totalCredit,
                'is_balanced'    => $isBalanced,
            ]);

            // 6. Crear líneas de detalle
            foreach ($entriesData as $entry) {
                $journal->entries()->create($entry);
            }

            return $journal;
        });
    }
}