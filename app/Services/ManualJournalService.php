<?php

namespace App\Services;

use App\Models\Accounting\Journal;
use App\Services\OwnerService;
use Illuminate\Support\Facades\DB;
use Exception;

class ManualJournalService
{
    protected OwnerService $ownerService;

    public function __construct(OwnerService $ownerService)
    {
        $this->ownerService = $ownerService;
    }

    public function registerManualJournal(int $year, int $month, string $date, string $description, array $entries): Journal
    {
        $activeOwner = $this->ownerService->getActiveOwner();

        if (!$activeOwner) {
            throw new Exception("No hay un Owner activo seleccionado. No se puede generar el asiento.");
        }

        $ownerId = $activeOwner->id;

        return DB::transaction(function () use ($ownerId, $year, $month, $date, $description, $entries) {
            
            $totalDebit = 0;
            $totalCredit = 0;
            $formattedEntries = [];

            // 1. Procesar y validar las líneas de detalle enviadas
            foreach ($entries as $entry) {
                $debit = (float) ($entry['debit'] ?? 0);
                $credit = (float) ($entry['credit'] ?? 0);

                if ($debit > 0 || $credit > 0) {
                    $totalDebit += $debit;
                    $totalCredit += $credit;

                    $formattedEntries[] = [
                        'account_code'   => $entry['account_code'],
                        'component_name' => 'manual',
                        'debit'          => $debit,
                        'credit'         => $credit,
                    ];
                }
            }

            // 2. Validación estricta de partida doble
            $isBalanced = round($totalDebit, 2) === round($totalCredit, 2);

            if (!$isBalanced) {
                throw new Exception("El asiento contable no está cuadrado. Total Debe: {$totalDebit}, Total Haber: {$totalCredit}");
            }

            // 3. Calcular el siguiente número correlativo de asiento para ESTE owner y ESTE año
            $lastEntryNumber = Journal::where('owner_id', $ownerId)
                ->where('year', $year)
                ->lockForUpdate()
                ->max('entry_number');

            $nextEntryNumber = ($lastEntryNumber ?? 0) + 1;

            // 4. Crear cabecera del Asiento de forma directa e independiente (sin documento)
            $journal = Journal::create([
                'vc_document_id' => null, // 100% independiente de documentos de compra/venta
                'owner_id'       => $ownerId,
                'year'           => $year,
                'month'          => $month,
                'date'           => $date,
                'description'    => $description,
                'entry_number'   => $nextEntryNumber,
                'total_debit'    => $totalDebit,
                'total_credit'   => $totalCredit,
                'is_balanced'    => $isBalanced,
            ]);

            // 5. Crear líneas de detalle usando la relación
            foreach ($formattedEntries as $entry) {
                $journal->entries()->create($entry);
            }

            return $journal;
        });
    }

    public function updateManualJournal(Journal $journal, int $year, int $month, string $date, string $description, array $entries): Journal
    {
        if ($journal->vc_document_id) {
            throw new Exception("Este asiento está asociado a un documento V/C y no puede actualizarse como manual.");
        }

        return DB::transaction(function () use ($journal, $year, $month, $date, $description, $entries) {
            
            $totalDebit = 0;
            $totalCredit = 0;
            $formattedEntries = [];

            // 1. Procesar y validar las líneas de detalle enviadas
            foreach ($entries as $entry) {
                $debit = (float) ($entry['debit'] ?? 0);
                $credit = (float) ($entry['credit'] ?? 0);

                if ($debit > 0 || $credit > 0) {
                    $totalDebit += $debit;
                    $totalCredit += $credit;

                    $formattedEntries[] = [
                        'account_code'   => $entry['account_code'],
                        'component_name' => 'manual',
                        'debit'          => $debit,
                        'credit'         => $credit,
                    ];
                }
            }

            // 2. Validación estricta de partida doble
            $isBalanced = round($totalDebit, 2) === round($totalCredit, 2);

            if (!$isBalanced) {
                throw new Exception("El asiento contable no está cuadrado. Total Debe: {$totalDebit}, Total Haber: {$totalCredit}");
            }

            // 3. Actualizar la cabecera del asiento
            $journal->update([
                'year'         => $year,
                'month'        => $month,
                'date'         => $date,
                'description'  => $description,
                'total_debit'  => $totalDebit,
                'total_credit' => $totalCredit,
                'is_balanced'  => $isBalanced,
            ]);

            // 4. Reemplazar las líneas de detalle de forma limpia
            $journal->entries()->delete();
            foreach ($formattedEntries as $entry) {
                $journal->entries()->create($entry);
            }

            return $journal;
        });
    }
}