<?php

namespace App\Http\Controllers\VCDocuments;

use App\Models\Masters\Entity;
use App\Models\Masters\DocumentType;
use App\Models\VCDocuments\VCDocument;
use Illuminate\Http\Request;

class VCDocumentController
{
    public function create()
    {
        return view('vc_documents.create');
    }

    public function checkEntity($rut)
    {
        $entity = Entity::where('rut', $rut)->first();
        if ($entity) {
            return response()->json(['exists' => true, 'name' => $entity->name]);
        }
        return response()->json(['exists' => false]);
    }

    public function checkDocumentType($doctype)
    {
        $docType = DocumentType::where('doctype', $doctype)->first();
        if ($docType) {
            return response()->json(['exists' => true, 'name' => $docType->name]);
        }
        return response()->json(['exists' => false]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'month_register'       => 'required|integer|min:1|max:12',
            'year_register'        => 'required|integer|min:2000',
            'type_vc'              => 'required|string|max:1',
            'rut'                  => 'required|string|max:10',
            'entity_name'          => 'required|string|max:100',
            'doctype'              => 'required|integer',
            'document_type_name'   => 'required|string|max:50',
            'folio'                => 'required|integer',
            'date'                 => 'required|date',
            
            'rut_ref'              => 'nullable|string|max:10',
            'folio_ref'            => 'nullable|integer',
            'td_ref'               => 'nullable|string|max:1',
            'date_centralize'      => 'nullable|date',
            
            'net'                  => 'nullable|integer',
            'exempt'               => 'nullable|integer',
            'vat_rec'              => 'nullable|integer',
            'vat_no_rec'           => 'nullable|integer',
            'plus_oth_tax'         => 'nullable|integer',
            'minus_oth_tax'        => 'nullable|integer',
            'total'                => 'required|integer',
        ]);

        // 1. Manejar la Entidad
        $entity = Entity::firstOrCreate(
            ['rut' => $validated['rut']],
            ['name' => $validated['entity_name']]
        );

        // 2. Manejar el Tipo de Documento
        $documentType = DocumentType::firstOrCreate(
            ['doctype' => $validated['doctype']],
            ['name' => $validated['document_type_name']]
        );

        // 3. Comprobar si el documento ya existe (Entidad + Tipo de Documento + Folio)
        $exists = VCDocument::where('entity_id', $entity->id)
            ->where('document_type_id', $documentType->id)
            ->where('folio', $validated['folio'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'duplicate' => 'El documento con este RUT, Tipo de Documento y Folio ya se encuentra registrado.'
            ]);
        }

        // 4. Preparar datos finales agregando los IDs correspondientes
        $validated['entity_id'] = $entity->id;
        $validated['document_type_id'] = $documentType->id;

        // Limpiar campos temporales
        unset($validated['rut'], $validated['entity_name'], $validated['doctype'], $validated['document_type_name']);

        // 5. Guardar el documento
        VCDocument::create($validated);

        return redirect()->route('vc_documents.create')
                         ->with('success', 'Documento V/C guardado exitosamente.');
    }
}