<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Accounts\AccountTemplate;
use App\Models\Accounts\AccountTemplateItem;
use Database\Seeders\AccountSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class AccountTemplateController extends Controller
{
    public function index()
    {
        $templates = AccountTemplate::withCount('items')->get();
        return view('masters.account_templates_index', compact('templates'));
    }

    // Crea un nuevo plan clonando la base del AccountSeeder
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:account_templates,name',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $slug = Str::slug($request->name);

                $template = AccountTemplate::create([
                    'slug' => $slug,
                    'name' => $request->name,
                    'description' => $request->description,
                ]);

                // Obtenemos las cuentas base del AccountSeeder existente
                $baseAccounts = AccountSeeder::getTemplateAccounts();

                foreach ($baseAccounts as $acc) {
                    AccountTemplateItem::create([
                        'account_template_id' => $template->id,
                        'code' => $acc['code'],
                        'name' => $acc['name'],
                        'category' => $acc['category'],
                    ]);
                }
            });

            return redirect()->route('masters.account_templates.index')
                ->with('success', 'Plan de cuentas creado y clonado exitosamente desde la base.');

        } catch (Exception $e) {
            return back()->with('error', 'Error al crear el plan: ' . $e->getMessage());
        }
    }

    // Vista de edición de las cuentas del plan
    public function edit(AccountTemplate $accountTemplate)
    {
        $accountTemplate->load('items');
        return view('masters.account_templates_edit', compact('accountTemplate'));
    }

    // Actualizar nombre o añadir/editar cuentas del plan maestro
    public function update(Request $request, AccountTemplate $accountTemplate)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:account_templates,name,' . $accountTemplate->id,
            'items.*.code' => 'required|string|max:20',
            'items.*.name' => 'required|string|max:150',
            'items.*.category' => 'required|string',
        ]);

        try {
            DB::transaction(function () use ($request, $accountTemplate) {
                $accountTemplate->update([
                    'name' => $request->name,
                    'description' => $request->description,
                ]);

                // Actualizar o sincronizar cuentas del plan
                if ($request->has('items')) {
                    // Mapeamos los ítems enviados
                    $existingIds = [];
                    foreach ($request->items as $id => $itemData) {
                        if (str_starts_with($id, 'new_')) {
                            // Crear nueva cuenta en el plan
                            AccountTemplateItem::create([
                                'account_template_id' => $accountTemplate->id,
                                'code' => $itemData['code'],
                                'name' => $itemData['name'],
                                'category' => $itemData['category'],
                            ]);
                        } else {
                            // Actualizar cuenta existente
                            $item = AccountTemplateItem::where('account_template_id', $accountTemplate->id)->find($id);
                            if ($item) {
                                $item->update($itemData);
                                $existingIds[] = $item->id;
                            }
                        }
                    }

                    // Eliminar las que fueron removidas de la tabla en la interfaz
                    AccountTemplateItem::where('account_template_id', $accountTemplate->id)
                        ->when(!empty($existingIds), function($query) use ($existingIds) {
                            return $query->whereNotIn('id', $existingIds);
                        })->delete();
                }
            });

            return redirect()->route('masters.account_templates.index')
                ->with('success', 'Plan de cuentas actualizado correctamente.');

        } catch (Exception $e) {
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function destroy(AccountTemplate $accountTemplate)
    {
        $accountTemplate->delete();
        return redirect()->route('masters.account_templates.index')
            ->with('success', 'Plan maestro eliminado exitosamente.');
    }
}