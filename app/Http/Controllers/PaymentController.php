<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function store(StorePaymentRequest $request)
    {
        $payment = Payment::create([
            'association_id' => (int) $request->integer('association_id'),
            'user_id' => $request->user()->id,
            'fee_id' => $request->input('fee_id'),
            'amount' => $request->input('amount'),
            'status' => 'paid',
            'paid_at' => $request->date('paid_at') ?? now(),
            'reference' => $request->input('reference'),
        ]);

        return response()->json($payment, 201);
    }
}
