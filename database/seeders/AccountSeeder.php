<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Accounts\Account;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['code' => '410101', 'name' => 'Ventas'],
            ['code' => '210201', 'name' => 'IVA Débito'],
            ['code' => '110102', 'name' => 'Clientes'],
            ['code' => '110101', 'name' => 'Compras'],
            ['code' => '110201', 'name' => 'IVA Crédito'],
            ['code' => '210101', 'name' => 'Proveedores'],
        ];

        foreach ($accounts as $account) {
            Account::updateOrCreate(
                ['code' => $account['code']],
                ['name' => $account['name']]
            );
        }
    }
}