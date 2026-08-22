<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Accounts\Account;
use App\Services\PaymentService;
use App\Services\OwnerService;
use Illuminate\Http\Request;
use Exception;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(OwnerService $ownerService)
    {
        $activeOwner = $ownerService->getActiveOwner();
        $pendingDocuments = $this->paymentService->getPendingBalanceDocuments();
        
        // Obtenemos cuentas bancarias o de activos para los pagos (ej: cuentas que comiencen por caja/banco o todas las cuentas)
        $accounts = Account::orderBy('code', 'asc')->get();

        return view('accounting.payments_index', compact('activeOwner', 'pendingDocuments', 'accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'document_id' => 'required|exists:vc_documents,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'bank_account_code' => 'required|string|exists:accounts,code',
        ]);

        try {
            $this->paymentService->processPayment(
                documentId: (int) $request->document_id,
                amount: (float) $request->amount,
                date: $request->date,
                bankAccountCode: $request->bank_account_code
            );

            return back()->with('success', 'Pago/Cobro procesado y registrado contablemente con éxito.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Error al procesar el pago: ' . $e->getMessage());
        }
    }
}