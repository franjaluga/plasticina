<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VCDocuments\VCDocumentController;
use App\Http\Controllers\Owners\OwnerController;
use App\Services\OwnerService;
use App\Models\VCDocuments\VCDocument;
use App\Http\Controllers\Accounting\AccountingReportController;
use App\Http\Controllers\Accounting\RetrievalDocumentController;
use App\Http\Controllers\Accounting\DocumentQueryController;
use App\Http\Controllers\Accounting\LedgerController;
use App\Http\Controllers\Accounting\ManualJournalController;
use App\Http\Controllers\Accounting\AuditController;
use App\Http\Controllers\Accounting\PaymentController;


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
})->name('welcome');

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

// BALANCE
Route::get('/vc-documents/balance-tributario', [AccountingReportController::class, 'taxBalance'])
    ->name('vc_documents.tax_balance');

Route::get('/accounting/reports', [AccountingReportController::class, 'index'])
    ->name('accounting.reports.index');

// CONSULTA DE DOCUMENTOS (Debe ir antes de la ruta con {id})
Route::get('/accounting/documents/query', [DocumentQueryController::class, 'index'])
    ->name('vc_documents.query');

// RECUPERAR DOCUMENTO
Route::get('/accounting/documents/{id}', [RetrievalDocumentController::class, 'show'])
    ->name('accounting.documents.detail');

// MAYOR
Route::get('/accounting/ledger', [LedgerController::class, 'index'])
    ->name('accounting.ledger');

// MANUAL JOURNAL
Route::get('/accounting/manual-journals/create', [ManualJournalController::class, 'create'])
    ->name('accounting.manual_journals.create');

Route::post('/accounting/manual-journals', [ManualJournalController::class, 'store'])
    ->name('accounting.manual_journals.store');

// AUDITORIA
Route::get('/accounting/reports/audit', [AuditController::class, 'index'])->name('accounting.reports.audit');

// BORRADO
Route::delete('/accounting/journals/{journal}', [AuditController::class, 'destroy'])->name('accounting.journals.destroy');

// COBROS Y PAGOS
Route::get('/accounting/payments', [PaymentController::class, 'index'])->name('accounting.payments.index');
Route::post('/accounting/payments', [PaymentController::class, 'store'])->name('accounting.payments.store');