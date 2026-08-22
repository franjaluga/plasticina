<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Accounts\Account;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // Activos (1)
            ['code' => '110101', 'name' => 'Caja / Banco', 'category' => 'activo'],
            ['code' => '110102', 'name' => 'Clientes', 'category' => 'activo'],
            ['code' => '110201', 'name' => 'IVA Crédito Fiscal', 'category' => 'activo'],
            
            // Pasivos (2)
            ['code' => '210101', 'name' => 'Proveedores Nacionales', 'category' => 'pasivo'],
            ['code' => '210201', 'name' => 'IVA Débito Fiscal', 'category' => 'pasivo'],

            // Patrimonio (3)
            ['code' => '310101', 'name' => 'Capital Social', 'category' => 'patrimonio'],

            // Ingresos / Ganancia (4)
            ['code' => '410101', 'name' => 'Ventas de Explotación', 'category' => 'ganancia'],

            // Costos y Gastos / Pérdida (5 o 6)
            ['code' => '510101', 'name' => 'Costo de Ventas', 'category' => 'perdida'],
            ['code' => '510201', 'name' => 'Gastos Generales', 'category' => 'perdida'],
        ];

        foreach ($accounts as $acc) {
            Account::updateOrCreate(
                ['code' => $acc['code']],
                $acc
            );
        }
    }
}