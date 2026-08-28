<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Owners\Owner;
use App\Models\Accounts\Account;
use Database\Seeders\AccountSeeder;

class OwnerSeeder extends Seeder
{
    public function run(): void
    {
        $owner = Owner::updateOrCreate(
            ['rut' => '55555555-5'],
            [
                'name'              => 'Test',
                'is_active'         => 1,
                'account_plan_type' => 'standard_pyme',
            ]
        );

        if ($owner->accounts()->count() === 0) {
            $templateAccounts = AccountSeeder::getTemplateAccounts();

            foreach ($templateAccounts as $acc) {
                Account::create([
                    'owner_id' => $owner->id,
                    'code'     => $acc['code'],
                    'name'     => $acc['name'],
                    'category' => $acc['category'],
                ]);
            }
        }
    }
}