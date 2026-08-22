<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VCDocuments\VCDocumentController;
use App\Http\Controllers\Owners\OwnerController;
use App\Services\OwnerService;

Route::get('/', function (OwnerService $ownerService) {
    $activeOwner = $ownerService->getActiveOwner();
    return view('welcome', compact('activeOwner'));
});

// OWNERS (EMPRESAS)
Route::resource('owners', OwnerController::class)->except(['show', 'create', 'edit']);
Route::patch('owners/{owner}/activate', [OwnerController::class, 'activate'])->name('owners.activate');

// VIA FORMULARIO
Route::get('/vc-documents/create', [VCDocumentController::class, 'create'])->name('vc_documents.create');
Route::post('/vc-documents', [VCDocumentController::class, 'store'])->name('vc_documents.store');
Route::get('/vc-documents/check-entity/{rut}', [VCDocumentController::class, 'checkEntity']);
Route::get('/vc-documents/check-doctype/{doctype}', [VCDocumentController::class, 'checkDocumentType']);

// VIA CSV
Route::get('/vc_documents/upload', function () {
    return view('vc_documents.upload');
})->name('vc_documents.upload'); 

Route::post('/vc_documents', [VCDocumentController::class, 'store'])
     ->name('vc_documents.store');
     
Route::post('/vc_documents/csv', [VCDocumentController::class, 'csvImport'])
     ->name('vc_documents.csv');

     