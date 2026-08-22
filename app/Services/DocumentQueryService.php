<?php

namespace App\Services;

use App\Models\VCDocuments\VCDocument;
use Illuminate\Database\Eloquent\Collection;

class DocumentQueryService
{
    public function getFilteredDocuments(string $typeVc, $month, $year): Collection
    {
        $activeOwner = app(OwnerService::class)->getActiveOwner();

        return VCDocument::with(['entity', 'documentType', 'journal'])
            ->where('owner_id', $activeOwner?->id)
            ->where('type_vc', strtoupper(trim($typeVc)))
            ->where('month_register', (int) $month)
            ->where('year_register', (int) $year)
            ->orderBy('folio', 'asc')
            ->get();
    }
}