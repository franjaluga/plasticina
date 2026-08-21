<?php

namespace App\Http\Controllers\VCDocuments;

use App\Models\Masters\Entity;
use App\Models\Masters\DocumentType;
use App\Models\VCDocuments\VCDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;          // para la validación de CSV
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class VCDocumentController
{
    /* ------------------------------------------------------------------
     *  Vistas
     * ------------------------------------------------------------------ */

    public function create()
    {
        return view('vc_documents.create');
    }

    /* ------------------------------------------------------------------
     *  Validaciones auxiliares (Ajax)
     * ------------------------------------------------------------------ */

    public function checkEntity($rut)
    {
        $entity = Entity::where('rut', $rut)->first();

        return response()->json([
            'exists' => !!$entity,
            'name'   => $entity->name ?? null,
        ]);
    }

    public function checkDocumentType($doctype)
    {
        $docType = DocumentType::where('doctype', $doctype)->first();

        return response()->json([
            'exists' => !!$docType,
            'name'   => $docType->name ?? null,
        ]);
    }

    /* ------------------------------------------------------------------
     *  Lógica de guardado (tanto para formularios como para CSV)
     * ------------------------------------------------------------------ */

    /**
     * Guarda un documento a partir de los datos validados.
     *
     * @param array $validated Los datos ya validados y con el formato correcto
     * @return VCDocument El modelo creado
     */
    private function persistDocument(array $validated): VCDocument
    {
        // 1. Entidad (o crearla)
        $entity = Entity::firstOrCreate(
            ['rut' => $validated['rut']],
            ['name' => $validated['entity_name']]
        );

        // 2. Tipo de documento (o crearlo)
        $documentType = DocumentType::firstOrCreate(
            ['doctype' => $validated['doctype']],
            ['name' => $validated['document_type_name']]
        );

        // 3. Evitar duplicados
        $exists = VCDocument::where('entity_id', $entity->id)
                            ->where('document_type_id', $documentType->id)
                            ->where('folio', $validated['folio'])
                            ->exists();

        if ($exists) {
            throw new \Exception(
                'El documento con este RUT, Tipo de Documento y Folio ya se encuentra registrado.'
            );
        }

        // 4. Prepara los datos finales
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

        // 5. Persistir y devolver
        return VCDocument::create($validated);
    }

    /* ------------------------------------------------------------------
     *  Almacena desde el formulario web
     * ------------------------------------------------------------------ */

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

        try {
            $this->persistDocument($validated);

            return redirect()
                    ->route('vc_documents.create')
                    ->with('success', 'Documento V/C guardado exitosamente.');
        } catch (\Exception $e) {
            return back()
                   ->withInput()
                   ->withErrors(['duplicate' => $e->getMessage()]);
        }
    }

    /* ------------------------------------------------------------------
     *  Importación CSV
     * ------------------------------------------------------------------ */

    /**
     * Subir un archivo CSV y crear documentos línea por línea.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function csvImport(Request $request)
    {
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $uploadedFile = $request->file('csv_file');

        if (! $uploadedFile || ! $uploadedFile->isValid()) {
            return back()->withErrors(['csv_file' => 'Archivo inválido']);
        }

        $handle = fopen($uploadedFile->getRealPath(), 'r');
        if ($handle === false) {
            return back()->withErrors(['csv_file' => 'No se pudo abrir el archivo CSV.']);
        }

        DB::beginTransaction();

        try {
            $headers = null;
            $rowNumber = 0;
            $errorsFound = [];

            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;

                if ($headers === null) {
                    $headers = $row;
                    continue;
                }

                if (empty(array_filter($row))) {
                    continue;
                }

                if (count($headers) !== count($row)) {
                    $errorsFound[] = "Línea $rowNumber: Las columnas no coinciden con los encabezados.";
                    continue;
                }

                $data = array_combine($headers, $row);

                // --- LIMPIEZA Y TRANSFORMACIÓN DE DATOS ---

                // 1. Fecha principal (DD/MM/YYYY a YYYY-MM-DD)
                if (!empty($data['date'])) {
                    $cleanDate = str_replace('/', '-', trim($data['date']));
                    $timestamp = strtotime($cleanDate);
                    if ($timestamp) {
                        $data['date'] = date('Y-m-d', $timestamp);
                    }
                }

                // 2. Fecha de centralización (Forzar a null si viene vacía o inválida)
                $centralizeRaw = trim($data['date_centralize'] ?? '');
                if ($centralizeRaw === '' || $centralizeRaw === 'NULL' || $centralizeRaw === 'null') {
                    $data['date_centralize'] = null;
                } else {
                    $cleanCentDate = str_replace('/', '-', $centralizeRaw);
                    $timestampCent = strtotime($cleanCentDate);
                    if ($timestampCent) {
                        $data['date_centralize'] = date('Y-m-d', $timestampCent);
                    } else {
                        $data['date_centralize'] = null; // Si tiene texto extraño, lo anulamos
                    }
                }

                // 3. Limitar td_ref a 1 carácter si existe
                if (!empty($data['td_ref'])) {
                    $data['td_ref'] = substr(trim($data['td_ref']), 0, 1);
                }

                // 4. Limpiar otros campos opcionales vacíos
                foreach (['rut_ref', 'folio_ref', 'net', 'exempt', 'vat_rec', 'vat_no_rec', 'plus_oth_tax', 'minus_oth_tax'] as $field) {
                    if (isset($data[$field]) && trim($data[$field]) === '') {
                        $data[$field] = null;
                    }
                }
                // ------------------------------------------

                $validator = Validator::make($data, [
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

                if ($validator->fails()) {
                    $mensajes = implode(', ', $validator->errors()->all());
                    $errorsFound[] = "Línea $rowNumber: " . $mensajes;
                    continue; 
                }

                $this->persistDocument($validator->validated());
            }

            if (!empty($errorsFound)) {
                DB::rollBack();
                if (is_resource($handle)) {
                    fclose($handle);
                }
                return back()->withErrors(['csv_file' => 'Errores en el CSV: ' . implode(' | ', $errorsFound)]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            if (is_resource($handle)) {
                fclose($handle);
            }

            return back()->withErrors(['csv_file' => 'Error al procesar CSV: '.$e->getMessage()]);
        } finally {
            if (is_resource($handle)) {
                fclose($handle);
            }
        }

        return redirect()
                ->route('vc_documents.create')
                ->with('success', 'CSV importado correctamente.');
    }
}
