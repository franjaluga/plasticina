<?php

namespace App\Http\Requests;

use App\Services\VCDocumentService;
use Illuminate\Foundation\Http\FormRequest;

class StoreVCDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return VCDocumentService::rules();
    }
}