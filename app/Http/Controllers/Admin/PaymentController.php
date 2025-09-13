<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'course'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('user')) {
            $query->where('user_id', (int) $request->input('user'));
        }
        if ($request->filled('course')) {
            $query->where('course_id', (int) $request->input('course'));
        }
        if ($request->filled('q')) {
            $q = (string) $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('stripe_session_id', 'like', "%$q%")
                    ->orWhere('stripe_payment_intent_id', 'like', "%$q%")
                    ->orWhere('paypal_order_id', 'like', "%$q%")
                    ->orWhere('paypal_capture_id', 'like', "%$q%")
                    ->orWhere('provider', 'like', "%$q%");
            });
        }

        $payments = $query->paginate(20)->withQueryString();

        return view('admin.payments.index', compact('payments'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['user', 'course']);
        return view('admin.payments.show', compact('payment'));
    }
}
