<?php

namespace App\Http\Controllers;

use App\Models\Payment;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['customer', 'lease.room'])->latest()->get();

        return view('payments.index', compact('payments'));
    }
}
