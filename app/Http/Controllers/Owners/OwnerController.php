<?php

namespace App\Http\Controllers\Owners;

use App\Models\Owners\Owner;
use App\Models\Accounts\Account;
use App\Services\OwnerService;
use Database\Seeders\AccountSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class OwnerController
{
    protected $ownerService;

    public function __construct(OwnerService $ownerService)
    {
        $this->ownerService = $ownerService;
    }

    public function index()
    {
        $owners = Owner::all();
        $activeOwner = $this->ownerService->getActiveOwner();
        $templates = \App\Models\Accounts\AccountTemplate::all(); // <-- Obtener planes creados

        return view('owners.index', compact('owners', 'activeOwner', 'templates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'rut' => 'required|string|max:10|unique:owners,rut',
            'name' => 'required|string|max:100',
            'account_plan_type' => 'required|string',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $planType = $request->account_plan_type;

                // 1. Crear el Owner primero para obtener su ID
                $owner = Owner::create([
                    'rut' => $request->rut,
                    'name' => $request->name,
                    'account_plan_type' => $planType,
                    'is_active' => false,
                ]);

                // 2. Obtener las cuentas de manera segura según el plan elegido
                if ($planType === 'standard_pyme') {
                    $templateAccounts = AccountSeeder::getTemplateAccounts();
                } else {
                    // Plan maestro personalizado creado desde /masters
                    $template = \App\Models\Accounts\AccountTemplate::where('slug', $planType)->with('items')->firstOrFail();
                    $templateAccounts = $template->items->map(function($item) {
                        return [
                            'code' => $item->code,
                            'name' => $item->name,
                            'category' => $item->category,
                        ];
                    })->toArray();
                }

                // 3. Crear cada cuenta asociándola estrictamente al nuevo owner_id
                foreach ($templateAccounts as $acc) {
                    \App\Models\Accounts\Account::create([
                        'owner_id' => $owner->id, // <-- ¡OBLIGATORIO AQUÍ!
                        'code'     => $acc['code'],
                        'name'     => $acc['name'],
                        'category' => $acc['category'],
                    ]);
                }
            });

            return redirect()->route('owners.index')->with('success', 'Owner y su plan de cuentas inicial creado exitosamente.');

        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Error al crear el owner: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Owner $owner)
    {
        $request->validate([
            'rut' => 'required|string|max:10|unique:owners,rut,' . $owner->id,
            'name' => 'required|string|max:100',
            // Nota: El plan de cuentas no se valida ni actualiza para mantener la inalterabilidad histórica
        ]);

        $owner->update([
            'rut' => $request->rut,
            'name' => $request->name,
        ]);

        return redirect()->route('owners.index')->with('success', 'Owner actualizado exitosamente.');
    }

    public function activate(Owner $owner)
    {
        // Desactiva a todos los demás y activa el seleccionado en una transacción segura
        DB::transaction(function () use ($owner) {
            Owner::where('is_active', true)->update(['is_active' => false]);
            $owner->update(['is_active' => true]);
        });

        return redirect()->route('owners.index')->with('success', 'Owner activado correctamente.');
    }

    public function show(Owner $owner)
    {
        // Cargar estadísticas o información adicional si lo deseas (ej: cantidad de cuentas, documentos)
        $owner->loadCount(['accounts']);
        
        return view('owners.show', compact('owner'));
    }
}