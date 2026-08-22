<?php

namespace App\Http\Requests;

use App\Services\VCDocumentService;
use App\Services\OwnerService;
use Illuminate\Foundation\Http\FormRequest;

class StoreVCDocumentRequest extends FormRequest
{
    public function authorize(OwnerService $ownerService): bool
    {
        return $ownerService->getActiveOwner() !== null;
    }

    public function rules(): array
    {
        return VCDocumentService::rules();
    }
    
    public function failedAuthorization()
    {
        throw new \Illuminate\Auth\Access\AuthorizationException(
            'No se puede procesar el documento porque no hay un Owner activo configurado.'
        );
    }
}