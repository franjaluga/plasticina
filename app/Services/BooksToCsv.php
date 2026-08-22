<?php

namespace App\Services;

use App\Services\DocumentAccountingService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BooksToCsv
{
    protected DocumentAccountingService $accountingService;

    public function __construct(DocumentAccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function export(): StreamedResponse
    {
        $journals = $this->accountingService->getJournalBookRecords();
        
        $filename = 'libro_diario_' . date('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function () use ($journals) {
            $file = fopen('php://output', 'w');

            // Forzar BOM en UTF-8 para que LibreOffice reconozca las tildes correctamente
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Cabeceras del CSV usando punto y coma ';' como separador
            fputcsv($file, [
                'Asiento ID',
                'Fecha',
                'Doc V/C ID',
                'Estado',
                'Codigo Cuenta',
                'Componente',
                'Debe',
                'Haber'
            ], ';');

            foreach ($journals as $journal) {
                $estado = $journal->is_balanced ? 'Cuadrado' : 'Descuadrado';

                foreach ($journal->entries as $entry) {
                    fputcsv($file, [
                        $journal->id,
                        $journal->date,
                        $journal->vc_document_id,
                        $estado,
                        $entry->account_code,
                        $entry->account->name ?? 'Sin nombre',
                        $entry->debit ?? 0,
                        $entry->credit ?? 0,
                    ], ';');
                }
            }

            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}