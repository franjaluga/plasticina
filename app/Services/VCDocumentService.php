<?php

namespace App\Services;

use App\Models\Masters\Entity;
use App\Models\Masters\DocumentType;
use App\Models\VCDocuments\VCDocument;
use App\Services\OwnerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;
use Throwable;

class VCDocumentService
{
    protected OwnerService $ownerService;

    public function __construct(OwnerService $ownerService)
    {
        $this->ownerService = $ownerService;
    }

    public static function rules(): array
    {
        return [
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
            'td_ref'               => 'nullable|integer|min:0|max:99',
            'date_centralize'      => 'nullable|date',
            'net'                  => 'nullable|integer',
            'exempt'               => 'nullable|integer',
            'vat_rec'              => 'nullable|integer',
            'vat_no_rec'           => 'nullable|integer',
            'plus_oth_tax'         => 'nullable|integer',
            'minus_oth_tax'        => 'nullable|integer',
            'total'                => 'required|integer',
        ];
    }

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
        // Validar que la fecha de centralización (date_centralize) pertenezca al año de trabajo activo
        $workingYear = (int) session('working_year', date('Y'));

        if (!empty($validated['date_centralize'])) {
            $centralizeYear = (int) date('Y', strtotime($validated['date_centralize']));

            if ($centralizeYear !== $workingYear) {
                throw new Exception("El documento no se puede ingresar: la fecha de centralización corresponde al año {$centralizeYear}, pero el año de trabajo activo es {$workingYear}.");
            }
        }

        // Validar la regla matemática del documento
        if (!$this->isDocumentBalanced($validated)) {
            throw new Exception('El documento no está cuadrado: los componentes no suman el total indicado.');
        }

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
            throw new Exception(
                'El documento con este RUT, Tipo de Documento y Folio ya se encuentra registrado.'
            );
        }

        $activeOwner = $this->ownerService->getActiveOwner();

        $validated = array_merge($validated, [
            'entity_id'         => $entity->id,
            'document_type_id'  => $documentType->doctype,
            'owner_id'          => $activeOwner ? $activeOwner->id : null,
        ]);

        unset(
            $validated['rut'],
            $validated['entity_name'],
            $validated['doctype'],
            $validated['document_type_name']
        );

        return VCDocument::create($validated);
    }

    public function importCsv($uploadedFile): int
    {
        if (! $uploadedFile || ! $uploadedFile->isValid()) {
            throw new Exception('Archivo inválido.');
        }

        $content = file_get_contents($uploadedFile->getRealPath());
        
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }
        
        $tempPath = tempnam(sys_get_temp_dir(), 'csv_utf8_');
        file_put_contents($tempPath, $content);

        $handle = fopen($tempPath, 'r');
        if ($handle === false) {
            throw new Exception('No se pudo abrir el archivo CSV procesado.');
        }

        DB::beginTransaction();

        try {
            $headers = null;
            $rowNumber = 0;
            $errorsFound = [];
            $rowsProcessed = 0;

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
                $data = $this->prepareCsvRowData($data);

                $validator = Validator::make($data, self::rules());

                if ($validator->fails()) {
                    $mensajes = implode(', ', $validator->errors()->all());
                    $errorsFound[] = "Línea $rowNumber: " . $mensajes;
                    continue; 
                }

                $this->persistDocument($validator->validated());
                $rowsProcessed++;
            }

            if (!empty($errorsFound)) {
                DB::rollBack();
                throw new Exception('Errores en el CSV: ' . implode(' | ', $errorsFound));
            }

            if ($rowsProcessed === 0) {
                DB::rollBack();
                throw new Exception('El archivo CSV no contiene filas de datos para procesar.');
            }

            DB::commit();
            return $rowsProcessed;

        } catch (Throwable $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        } finally {
            if (is_resource($handle)) {
                fclose($handle);
            }
            // Limpiar el archivo temporal
            if (isset($tempPath) && file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }
    }

    private function prepareCsvRowData(array $data): array
    {
        if (!empty($data['date'])) {
            $cleanDate = str_replace('/', '-', trim($data['date']));
            if ($timestamp = strtotime($cleanDate)) {
                $data['date'] = date('Y-m-d', $timestamp);
            }
        }

        $centralizeRaw = trim($data['date_centralize'] ?? '');
        if ($centralizeRaw === '' || strtolower($centralizeRaw) === 'null') {
            $data['date_centralize'] = null;
        } else {
            $cleanCentDate = str_replace('/', '-', $centralizeRaw);
            $timestampCent = strtotime($cleanCentDate);
            $data['date_centralize'] = $timestampCent ? date('Y-m-d', $timestampCent) : null;
        }

        if (!empty($data['td_ref'])) {
            $data['td_ref'] = substr(trim($data['td_ref']), 0, 2);
        }

        foreach (['rut_ref', 'folio_ref', 'net', 'exempt', 'vat_rec', 'vat_no_rec', 'plus_oth_tax', 'minus_oth_tax'] as $field) {
            if (isset($data[$field]) && trim($data[$field]) === '') {
                $data[$field] = null;
            }
        }

        return $data;
    }

    private function isDocumentBalanced(array $data): bool
    {
        $net = (int) ($data['net'] ?? 0);
        $exempt = (int) ($data['exempt'] ?? 0);
        $vatRec = (int) ($data['vat_rec'] ?? 0);
        $vatNoRec = (int) ($data['vat_no_rec'] ?? 0);
        $plusOthTax = (int) ($data['plus_oth_tax'] ?? 0);
        $minusOthTax = (int) ($data['minus_oth_tax'] ?? 0);
        $total = (int) ($data['total'] ?? 0);

        $calculatedTotal = $net + $exempt + $vatRec + $vatNoRec + $plusOthTax - $minusOthTax;

        return $calculatedTotal === $total;
    }
}