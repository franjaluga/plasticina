<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VCDocuments\VCDocumentController;
use App\Http\Controllers\Owners\OwnerController;
use App\Services\OwnerService;
use App\Models\VCDocuments\VCDocument;
use App\Models\Accounts\Account;
use App\Http\Controllers\Accounting\AccountingReportController;
use App\Http\Controllers\Accounting\RetrievalDocumentController;
use App\Http\Controllers\Accounting\DocumentQueryController;
use App\Http\Controllers\Accounting\LedgerController;
use App\Http\Controllers\Accounting\ManualJournalController;
use App\Http\Controllers\Accounting\AuditController;
use App\Http\Controllers\Accounting\PaymentController;
use App\Http\Controllers\Accounting\AccountTemplateController;
use App\Http\Controllers\Owners\AccountController;

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


/* ==========================================================
   AGRUPACIÓN 1: INGRESO MANUAL (V/C + Asientos Manuales)
   ================================================---------- */
Route::get('/ingress/manual', function () {
    return view('ingress.manual_index');
})->name('ingress.manual');

// V/C Formulario Individual
Route::get('/vc-documents/create', [VCDocumentController::class, 'create'])->name('vc_documents.create');
Route::post('/vc-documents', [VCDocumentController::class, 'store'])->name('vc_documents.store');
Route::get('/vc-documents/check-entity/{rut}', [VCDocumentController::class, 'checkEntity']);
Route::get('/vc-documents/check-doctype/{doctype}', [VCDocumentController::class, 'checkDocumentType']);

// Asientos Manuales
Route::get('/accounting/manual-journals/create', [ManualJournalController::class, 'create'])
    ->name('accounting.manual_journals.create');
Route::post('/accounting/manual-journals', [ManualJournalController::class, 'store'])
    ->name('accounting.manual_journals.store');


/* ==========================================================
   AGRUPACIÓN 2: IMPORTADOR (CSV)
   ================================================---------- */
Route::get('/ingress/import', function () {
    return view('vc_documents.upload');
})->name('ingress.import'); 

Route::post('/vc_documents/csv', [VCDocumentController::class, 'csvImport'])
     ->name('vc_documents.csv');


/* ==========================================================
   AGRUPACIÓN 3: REPORTES Y ANÁLISIS 
   ================================================---------- */
Route::get('/reports/analytics', function () {
    return view('ingress.analytics_index');
})->name('reports.analytics');

// NUEVAS RUTAS DE CONTEXTO (Libro Diario, Balance, Libro Mayor)
Route::get('/reports/journal-context', function () {
    return view('reports.journal_context');
})->name('reports.journal_context');

Route::get('/reports/balance-context', function () {
    return view('reports.balance_context');
})->name('reports.balance_context');

Route::get('/reports/ledger-context', function () {
    $accounts = Account::getActiveOwnerAccounts();
    return view('reports.ledger_context', compact('accounts'));
})->name('reports.ledger_context');

// Listado de documentos pendientes de contabilizar
Route::get('/vc-documents/pendientes', [VCDocumentController::class, 'pendingList'])
     ->name('vc_documents.pending');

// Contabilización masiva (lote)
Route::post('/vc-documents/contabilizar-masivo', [VCDocumentController::class, 'batchContabilizar'])
     ->name('vc_documents.batch_contabilizar');

// Libro Diario Contable V/C
Route::get('/vc-documents/libro-diario', [VCDocumentController::class, 'journalBook'])
     ->name('vc_documents.journal_book');
     
// Exportar a CSV Libro Diario
Route::get('/vc-documents/libro-diario/export-csv', [VCDocumentController::class, 'exportCsv'])
    ->name('vc_documents.export_csv');

// Balance Tributario y reportes generales
Route::get('/vc-documents/balance-tributario', [AccountingReportController::class, 'taxBalance'])
    ->name('vc_documents.tax_balance');

Route::get('/accounting/reports', [AccountingReportController::class, 'index'])
    ->name('accounting.reports.index');

// Consulta de Documentos
Route::get('/accounting/documents/query', [DocumentQueryController::class, 'index'])
    ->name('vc_documents.query');

// Recuperar Documento
Route::get('/accounting/documents/{id}', [RetrievalDocumentController::class, 'show'])
    ->name('accounting.documents.detail');

// Mayor Contable
Route::get('/accounting/ledger', [LedgerController::class, 'index'])
    ->name('accounting.ledger');

// Auditoría V/C y Borrado
Route::get('/accounting/reports/audit', [AuditController::class, 'index'])->name('accounting.reports.audit');

// Eliminar SOLO el asiento contable (el documento vuelve a pendientes de contabilizar)
Route::delete('/accounting/journals/{journal}/soft-delete', [AuditController::class, 'destroyJournalOnly'])->name('accounting.journals.destroy_journal_only');
Route::delete('/accounting/audit/{journal}/soft-delete', [AuditController::class, 'destroyJournalOnly'])->name('accounting.audit.destroy_journal_only');

// Eliminar el documento Y el asiento definitivamente (NO vuelve a pendientes)
Route::delete('/accounting/journals/{journal}/with-document', [AuditController::class, 'destroyWithDocument'])->name('accounting.journals.destroy_with_document');
Route::delete('/accounting/audit/{journal}/with-document', [AuditController::class, 'destroyWithDocument'])->name('accounting.audit.destroy_with_document');

// Cobros y Pagos
Route::get('/accounting/payments', [PaymentController::class, 'index'])->name('accounting.payments.index');
Route::post('/accounting/payments', [PaymentController::class, 'store'])->name('accounting.payments.store');

// Asientos del Sistema (Libro Diario General)
Route::get('/accounting/system-journals', [AccountingReportController::class, 'systemJournalsIndex'])
    ->name('accounting.system_journals');

Route::get('/accounting/journals/{id}/detail', [AccountingReportController::class, 'showJournalDetail'])
    ->name('accounting.journals.detail');

// ANALÍTICOS (Consulta V/C + Libro Mayor)
Route::get('/accounting/analytics', [AccountingReportController::class, 'analyticsIndex'])
    ->name('accounting.analytics');


// Mostrar el formulario de selección de fechas para el Libro Diario V/C
Route::get('/vc-documents/libro-diario/rango', function () {
    return view('reports.journal_date_range');
})->name('vc_documents.journal_book.form');

// La ruta que procesará el reporte utilizando el servicio
Route::get('/vc-documents/libro-diario/generar', [VCDocumentController::class, 'journalBook'])
    ->name('vc_documents.journal_book.generate');

// Edición de Asientos de V/C
Route::get('/accounting/journals/{id}/edit', [AccountingReportController::class, 'editJournal'])
    ->name('accounting.journals.edit');
Route::put('/accounting/journals/{id}', [AccountingReportController::class, 'updateJournal'])
    ->name('accounting.journals.update');

// Edición de Asientos Manuales
Route::get('/accounting/manual-journals/{id}/edit', [ManualJournalController::class, 'edit'])
    ->name('accounting.manual_journals.edit');
Route::put('/accounting/manual-journals/{id}', [ManualJournalController::class, 'update'])
    ->name('accounting.manual_journals.update');


Route::prefix('owners/{owner}/accounts')->name('owners.accounts.')->group(function () {
    Route::get('/', [AccountController::class, 'index'])->name('index');
    Route::post('/', [AccountController::class, 'store'])->name('store');
    Route::put('/{account}', [AccountController::class, 'update'])->name('update');
    Route::delete('/{account}', [AccountController::class, 'destroy'])->name('destroy');
});


Route::prefix('accounting/audit')->name('accounting.audit.')->group(function () {
    Route::get('/', [AuditController::class, 'index'])->name('index');
    // Asigna el nombre 'destroy' al método que prefieras (por ejemplo, destroyJournalOnly)
    Route::delete('/{journal}/only', [AuditController::class, 'destroyJournalOnly'])->name('destroy');
    // O si prefieres la otra opción:
    // Route::delete('/{journal}/document', [AuditController::class, 'destroyWithDocument'])->name('destroy');
});


// MAESTROS GENERALES (GESTION BASE DE PLANES DE CUENTAS PARA COPIAS)
Route::prefix('masters/account-templates')->name('masters.account_templates.')->group(function () {
    Route::get('/', [AccountTemplateController::class, 'index'])->name('index');
    Route::post('/', [AccountTemplateController::class, 'store'])->name('store');
    Route::get('/{accountTemplate}/edit', [AccountTemplateController::class, 'edit'])->name('edit');
    Route::put('/{accountTemplate}', [AccountTemplateController::class, 'update'])->name('update');
    Route::delete('/{accountTemplate}', [AccountTemplateController::class, 'destroy'])->name('destroy');
});

// CONFIGURACIÓN DEL SISTEMA
Route::get('/system/config', function () {
    return view('system.config');
})->name('system.config');


// OWNERS
Route::resource('owners', OwnerController::class)->except(['create', 'edit']);