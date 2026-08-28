<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Accounting\Journal;
use App\Services\OwnerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class AuditController extends Controller
{
    public function index(Request $request, OwnerService $ownerService)
    {
        $activeOwner = $ownerService->getActiveOwner();
        $workingYear = session('working_year', date('Y'));

        // Obtenemos los asientos del owner y año activo, cargando el documento y su entidad
        $journals = Journal::with(['entries.account', 'document.entity'])
            ->where('owner_id', $activeOwner?->id)
            ->where('year', $workingYear)
            ->orderBy('date', 'desc')
            ->orderBy('entry_number', 'desc')
            ->get();

        return view('accounting.audit_index', compact('journals', 'activeOwner', 'workingYear'));
    }

    // Método 1: Elimina SOLO el asiento contable y deja el documento pendiente
    public function destroyJournalOnly(Journal $journal)
    {
        try {
            DB::transaction(function () use ($journal) {
                // Verificamos si el asiento proviene de un documento V/C
                if ($journal->vc_document_id && $journal->document) {
                    $document = $journal->document;

                    // Marcar el documento V/C como pendiente de contabilizar
                    $document->update([
                        'is_centralized' => false,
                        'journal_id'     => null,
                    ]);
                }

                // Eliminar las líneas de detalle y el asiento
                $journal->entries()->delete();
                $journal->delete();
            });

            return redirect()->back()
                ->with('success', 'Asiento contable eliminado exitosamente. El documento asociado ha quedado pendiente de contabilizar.');
                
        } catch (Exception $e) {
            return back()->with('error', 'Error al eliminar el registro: ' . $e->getMessage());
        }
    }

    // Método 2: Elimina el documento Y el asiento definitivamente (NO vuelve a pendientes)
    public function destroyWithDocument(Journal $journal)
    {
        try {
            DB::transaction(function () use ($journal) {
                // Si tiene un documento asociado, lo eliminamos por completo de la base de datos
                if ($journal->document) {
                    $journal->document->delete();
                }

                // Eliminamos las líneas y el asiento contable
                $journal->entries()->delete();
                $journal->delete();
            });

            return redirect()->back()
                ->with('success', 'El documento y su asiento contable han sido eliminados definitivamente.');
                
        } catch (Exception $e) {
            return back()->with('error', 'Error al eliminar el registro: ' . $e->getMessage());
        }
    }
}