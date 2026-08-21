<?php

namespace App\Services;

use App\Models\Masters\Entity;
use App\Models\Masters\DocumentType;
use App\Models\VCDocuments\VCDocument;

class VCDocumentService
{
    public function getEntityByRut(string $rut): ?Entity
    {
        return Entity::where('rut', $rut)->first();
    }

    public function getDocumentTypeByDoctype(int|string $doctype): ?DocumentType
    {
        return DocumentType::where('doctype', $doctype)->first();
    }

    public function persistDocument(array $validated): VCDocument
    {
        $entity = Entity::firstOrCreate(
            ['rut' => $validated['rut']],
            ['name' => $validated['entity_name']]
        );

        $documentType = DocumentType::firstOrCreate(
            ['doctype' => $validated['doctype']],
            ['name' => $validated['document_type_name']]
        );

        $exists = VCDocument::where('entity_id', $entity->id)
                            ->where('document_type_id', $documentType->id)
                            ->where('folio', $validated['folio'])
                            ->exists();

        if ($exists) {
            throw new \Exception(
                'El documento con este RUT, Tipo de Documento y Folio ya se encuentra registrado.'
            );
        }

        $validated = array_merge($validated, [
            'entity_id'         => $entity->id,
            'document_type_id'  => $documentType->id,
        ]);

        unset(
            $validated['rut'],
            $validated['entity_name'],
            $validated['doctype'],
            $validated['document_type_name']
        );

        return VCDocument::create($validated);
    }
}