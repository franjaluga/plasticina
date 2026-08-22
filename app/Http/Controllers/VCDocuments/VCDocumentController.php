<?php

namespace App\Http\Controllers\VCDocuments;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVCDocumentRequest;
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

    public function store(StoreVCDocumentRequest $request, VCDocumentService $service)
    {
        try {
            $service->persistDocument($request->validated());

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