<?php

namespace App\Services;

use App\Models\Accounting\Journal;
use App\Services\OwnerService;
use Illuminate\Support\Collection;

class TaxBalanceService
{
    protected OwnerService $ownerService;

    public function __construct(OwnerService $ownerService)
    {
        $this->ownerService = $ownerService;
    }

    public function generateBalance(int $year): Collection
    {
        $activeOwner = $this->ownerService->getActiveOwner();

        if (!$activeOwner) {
            return collect();
        }

        $journals = Journal::where('owner_id', $activeOwner->id)
            ->where('year', $year)
            ->with(['entries.account'])
            ->get();

        $entries = $journals->flatMap(function ($journal) {
            return $journal->entries;
        });

        $grouped = $entries->groupBy('account_code');
        $balanceRows = collect();

        foreach ($grouped as $code => $accountEntries) {
            $firstAccount = $accountEntries->first()->account;
            $accountName = $firstAccount->name ?? 'Cuenta Sin Nombre';
            
            // Si la cuenta existe en la BD usamos su categoría; si no, la inferimos por el primer dígito del código
            if ($firstAccount && !empty($firstAccount->category)) {
                $category = strtolower(trim($firstAccount->category));
            } else {
                $firstDigit = substr($code, 0, 1);
                $category = match ($firstDigit) {
                    '1' => 'activo',
                    '2' => 'pasivo',
                    '3' => 'patrimonio',
                    '4' => 'ganancia',
                    default => 'perdida', // 5 o superior (costos y gastos)
                };
            }
            
            $totalDebit = $accountEntries->sum('debit');
            $totalCredit = $accountEntries->sum('credit');

            $balanceDebit = 0;
            $balanceCredit = 0;
            $diff = $totalDebit - $totalCredit;

            if ($diff > 0) {
                $balanceDebit = $diff;
            } elseif ($diff < 0) {
                $balanceCredit = abs($diff);
            }

            $activo = 0;
            $pasivo = 0;
            $perdida = 0;
            $ganancia = 0;

            // Tratamiento contable basado en la categoría validada
            switch ($category) {
                case 'activo':
                    $activo = $balanceDebit - $balanceCredit;
                    if ($activo < 0) { 
                        $pasivo = abs($activo); 
                        $activo = 0; 
                    }
                    break;

                case 'pasivo':
                case 'patrimonio':
                    $pasivo = $balanceCredit - $balanceDebit;
                    if ($pasivo < 0) { 
                        $activo = abs($pasivo); 
                        $pasivo = 0; 
                    }
                    break;

                case 'ganancia':
                    $ganancia = $balanceCredit - $balanceDebit;
                    if ($ganancia < 0) {
                        $perdida = abs($ganancia);
                        $ganancia = 0;
                    }
                    break;

                case 'perdida':
                default:
                    $perdida = $balanceDebit - $balanceCredit;
                    if ($perdida < 0) {
                        $ganancia = abs($perdida);
                        $perdida = 0;
                    }
                    break;
            }

            $balanceRows->push([
                'code'           => $code,
                'name'           => $accountName,
                'sum_debit'      => $totalDebit,
                'sum_credit'     => $totalCredit,
                'balance_debit'  => $balanceDebit,
                'balance_credit' => $balanceCredit,
                'activo'         => $activo > 0 ? $activo : 0,
                'pasivo'         => $pasivo > 0 ? $pasivo : 0,
                'perdida'        => $perdida > 0 ? $perdida : 0,
                'ganancia'       => $ganancia > 0 ? $ganancia : 0,
            ]);
        }

        return $balanceRows->sortBy('code');
    }
}