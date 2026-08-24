<?php

namespace App\Services;

use App\Models\Accounting\Journal;
use App\Services\OwnerService;
use Illuminate\Database\Eloquent\Collection;

class JournalReportService
{
    protected OwnerService $ownerService;

    public function __construct(OwnerService $ownerService)
    {
        $this->ownerService = $ownerService;
    }

    public function getSystemJournals(?int $year = null, array $filters = []): Collection
    {
        $workingYear = $year ?? session('working_year', date('Y'));
        $activeOwner = $this->ownerService->getActiveOwner();

        if (!$activeOwner) {
            return new Collection();
        }

        $query = Journal::with(['document.entity', 'document.documentType', 'entries'])
            ->where('owner_id', $activeOwner->id)
            ->where('year', $workingYear);

        // 1. Filtro por Rango de Asientos (entry_number)
        if (!empty($filters['entry_from'])) {
            $query->where('entry_number', '>=', $filters['entry_from']);
        }
        if (!empty($filters['entry_to'])) {
            $query->where('entry_number', '<=', $filters['entry_to']);
        }

        // 2. Filtro por Rango de Fecha de Centralización (o fecha del asiento)
        if (!empty($filters['date_from'])) {
            $query->where(function($q) use ($filters) {
                $q->whereHas('document', fn($d) => $d->where('date_centralize', '>=', $filters['date_from']))
                  ->orWhere(fn($j) => $j->whereNull('vc_document_id')->where('date', '>=', $filters['date_from']));
            });
        }
        if (!empty($filters['date_to'])) {
            $query->where(function($q) use ($filters) {
                $q->whereHas('document', fn($d) => $d->where('date_centralize', '<=', $filters['date_to']))
                  ->orWhere(fn($j) => $j->whereNull('vc_document_id')->where('date', '<=', $filters['date_to']));
            });
        }

        // 3. Filtro por Rango de Folio de Documentos
        if (!empty($filters['folio_from'])) {
            $query->whereHas('document', fn($d) => $d->where('folio', '>=', $filters['folio_from']));
        }
        if (!empty($filters['folio_to'])) {
            $query->whereHas('document', fn($d) => $d->where('folio', '<=', $filters['folio_to']));
        }

        // 4. Filtro por RUT (asociado a la entidad del documento)
        if (!empty($filters['rut'])) {
            $query->whereHas('document.entity', fn($e) => $e->where('rut', 'LIKE', '%' . $filters['rut'] . '%'));
        }

        // 5. Filtro por Tipo de Documento
        if (!empty($filters['document_type_id'])) {
            $query->whereHas('document', function($d) use ($filters) {
                $d->where('document_type_id', $filters['document_type_id']);
            });
        }

        // 6. Filtro por Folio de Referencia
        if (!empty($filters['folio_ref'])) {
            $query->whereHas('document', fn($d) => $d->where('folio_ref', 'LIKE', '%' . $filters['folio_ref'] . '%'));
        }

        return $query->get();
    }
}