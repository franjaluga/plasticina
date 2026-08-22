<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VCDocuments\VCDocumentController;
use App\Http\Controllers\Owners\OwnerController;
use App\Services\OwnerService;
use App\Models\VCDocuments\VCDocument;

Route::get('/', function (OwnerService $ownerService) {
    $activeOwner = $ownerService->getActiveOwner();
    $workingYear = session('working_year', date('Y'));

    $pendingCount = 0;
    if ($activeOwner) {
        $pendingCount = VCDocument::where('owner_id', $activeOwner->id)
            ->where('year_register', $workingYear)
            ->doesntHave('journal')
            ->count();
    }

    return view('welcome', compact('activeOwner', 'pendingCount'));
});

// PERIODO CONTABLE
Route::post('/period/update', function (\Illuminate\Http\Request $request) {
    $request->validate(['working_year' => 'required|integer|min:2000']);
    session(['working_year' => $request->working_year]);
    return back()->with('success', 'Año de trabajo actualizado.');
})->name('period.update');


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

// Listado de documentos pendientes de contabilizar
Route::get('/vc-documents/pendientes', [VCDocumentController::class, 'pendingList'])
     ->name('vc_documents.pending');

// Contabilización masiva (lote)
Route::post('/vc-documents/contabilizar-masivo', [VCDocumentController::class, 'batchContabilizar'])
     ->name('vc_documents.batch_contabilizar');

// Libro Diario Contable
Route::get('/vc-documents/libro-diario', [VCDocumentController::class, 'journalBook'])
     ->name('vc_documents.journal_book');
     
// exportarlo a csv
Route::get('/vc-documents/libro-diario/export-csv', [VCDocumentController::class, 'exportCsv'])
    ->name('vc_documents.export_csv');