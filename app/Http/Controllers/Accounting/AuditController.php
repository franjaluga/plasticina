<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Accounting\Journal;
use App\Services\OwnerService;
use Illuminate\Http\Request;

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

    public function destroy(Journal $journal)
    {
        try {
            // Si el asiento está amarrado a un documento, borramos el documento (lo que eliminará el asiento en cascada)
            if ($journal->document) {
                $journal->document->delete();
            } else {
                // Si es asiento manual puro sin documento, borramos el asiento y sus líneas de detalle
                $journal->entries()->delete();
                $journal->delete();
            }

            return back()->with('success', 'Registro eliminado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el registro: ' . $e->getMessage());
        }
    }
}