<?php

return [
    'templates' => [
        'deposito_banco' => [
            'name' => 'Depósito a Banco (Caja → Banco)',
            'glosa' => 'Depósito de efectivo en cuenta corriente',
            'entries' => [
                ['account_code' => '1010102', 'debit_type' => 'amount', 'credit_type' => 0], // Bancos (Debe)
                ['account_code' => '1010101', 'debit_type' => 0, 'credit_type' => 'amount'], // Caja (Haber)
            ]
        ],
        'pago_patentes' => [
            'name' => 'Pago de Patentes',
            'glosa' => 'Pago de patente municipal',
            'entries' => [
                ['account_code' => '4010905', 'debit_type' => 'amount', 'credit_type' => 0], // Patente Municipal (Debe)
                ['account_code' => '1010102', 'debit_type' => 0, 'credit_type' => 'amount'], // Bancos (Haber)
            ]
        ],
    ]
];