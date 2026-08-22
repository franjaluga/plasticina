<?php

namespace App\Http\Controllers\Owners;

use App\Models\Owners\Owner;
use App\Services\OwnerService;
use Illuminate\Http\Request;

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

        return view('owners.index', compact('owners', 'activeOwner'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'rut' => 'required|string|max:10|unique:owners,rut',
            'name' => 'required|string|max:100',
        ]);

        Owner::create([
            'rut' => $request->rut,
            'name' => $request->name,
            'is_active' => false, // Por defecto inactivo al crear
        ]);

        return redirect()->route('owners.index')->with('success', 'Owner creado exitosamente.');
    }

    public function update(Request $request, Owner $owner)
    {
        $request->validate([
            'rut' => 'required|string|max:10|unique:owners,rut,' . $owner->id,
            'name' => 'required|string|max:100',
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
        \DB::transaction(function () use ($owner) {
            Owner::where('is_active', true)->update(['is_active' => false]);
            $owner->update(['is_active' => true]);
        });

        return redirect()->route('owners.index')->with('success', 'Owner activado correctamente.');
    }
}