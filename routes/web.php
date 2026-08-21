<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VCDocuments\VCDocumentController;

Route::get('/', function () {
    return view('welcome');
});

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