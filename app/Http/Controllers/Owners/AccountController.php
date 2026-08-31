<?php

namespace App\Http\Controllers\Owners;

use App\Http\Controllers\Controller;
use App\Models\Accounts\Account;
use App\Models\Owners\Owner;
use Illuminate\Http\Request;
use Exception;

class AccountController extends Controller
{
    // Muestra la lista de cuentas del owner seleccionado
    public function index(Owner $owner)
    {
        $accounts = $owner->accounts()->orderBy('code', 'asc')->get();
        return view('owners.own_accounts', compact('owner', 'accounts'));
    }
    // Guarda una nueva cuenta para este owner
    public function store(Request $request, Owner $owner)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:accounts,code,NULL,id,owner_id,' . $owner->id,
            'name' => 'required|string|max:150',
            'category' => 'required|in:activo,pasivo,patrimonio,perdida,ganancia',
        ]);

        try {
            $owner->accounts()->create([
                'code' => $request->code,
                'name' => $request->name,
                'category' => $request->category,
            ]);

            return back()->with('success', 'Cuenta contable creada exitosamente para esta empresa.');
        } catch (Exception $e) {
            return back()->with('error', 'Error al crear la cuenta: ' . $e->getMessage());
        }
    }

    // Actualiza una cuenta específica del owner
    public function update(Request $request, Owner $owner, Account $account)
    {
        // Validar que la cuenta pertenezca estrictamente al owner
        if ($account->owner_id !== $owner->id) {
            abort(403);
        }

        $request->validate([
            'code' => 'required|string|max:20|unique:accounts,code,' . $account->id . ',id,owner_id,' . $owner->id,
            'name' => 'required|string|max:150',
            'category' => 'required|in:activo,pasivo,patrimonio,perdida,ganancia',
        ]);

        try {
            $account->update([
                'code' => $request->code,
                'name' => $request->name,
                'category' => $request->category,
            ]);

            return back()->with('success', 'Cuenta contable actualizada correctamente.');
        } catch (Exception $e) {
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    // Elimina una cuenta del owner con validación de uso previo
    public function destroy(Owner $owner, Account $account)
    {
        if ($account->owner_id !== $owner->id) {
            abort(403);
        }

        try {
            // Validar si la cuenta está siendo utilizada en al menos un asiento contable del owner
            $isUsed = \DB::table('journal_entries')
                ->join('journals', 'journals.id', '=', 'journal_entries.journal_id')
                ->where('journals.owner_id', $owner->id)
                ->where('journal_entries.account_code', $account->code)
                ->exists();

            if ($isUsed) {
                return back()->with('error', 'No se puede eliminar la cuenta "' . $account->code . ' - ' . $account->name . '" porque tiene movimientos registrados en los asientos contables de esta empresa.');
            }

            // Si no tiene movimientos, se procede a eliminar de forma segura
            $account->delete();
            
            return back()->with('success', 'Cuenta contable eliminada exitosamente.');
            
        } catch (Exception $e) {
            return back()->with('error', 'Ocurrió un error al intentar eliminar la cuenta: ' . $e->getMessage());
        }
    }
}