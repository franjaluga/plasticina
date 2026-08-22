<?php

namespace App\Http\Controllers\VCDocuments;

use App\Http\Controllers\Controller;
use App\Services\VCDocumentService;
use Illuminate\Http\Request;

class VCDocumentController extends Controller
{
    public function create()
    {
        return view('vc_documents.create');
    }

    public function checkEntity($rut, VCDocumentService $service)
    {
        $entity = $service->getEntityByRut($rut);

        return response()->json([
            'exists' => (bool) $entity,
            'name'   => $entity->name ?? null,
        ]);
    }

    public function checkDocumentType($doctype, VCDocumentService $service)
    {
        $docType = $service->getDocumentTypeByDoctype($doctype);

        return response()->json([
            'exists' => (bool) $docType,
            'name'   => $docType->name ?? null,
        ]);
    }

    public function store(Request $request, VCDocumentService $service)
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

        try {
            $service->persistDocument($validated);

            return redirect()
                ->route('vc_documents.create')
                ->with('success', 'Documento V/C guardado exitosamente.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['duplicate' => $e->getMessage()]);
        }
    }

    public function csvImport(Request $request, VCDocumentService $service)
    {
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        try {
            $rowsProcessed = $service->importCsv($request->file('csv_file'));

            return redirect()
                ->route('vc_documents.create')
                ->with('success', "¡CSV importado correctamente! Se ingresaron {$rowsProcessed} documentos.");
                
        } catch (\Exception $e) {
            return back()->withErrors(['csv_file' => $e->getMessage()]);
        }
    }
}