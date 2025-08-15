<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GiftCard;
use App\Models\Course;
use App\Models\Payment;
use Illuminate\Http\Request;

class GiftCardController extends Controller
{
    public function index(Request $request)
    {
        $query = GiftCard::query()->with(['course', 'buyer', 'redeemer']);

        // Filtri
        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }
        if ($courseId = $request->integer('course_id')) {
            $query->where('course_id', $courseId);
        }
        if ($code = $request->string('code')->toString()) {
            $query->where('code', 'like', '%'.$code.'%');
        }
        if ($email = $request->string('email')->toString()) {
            $query->where(function ($q) use ($email) {
                $q->where('recipient_email', 'like', '%'.$email.'%')
                  ->orWhereHas('buyer', function ($q2) use ($email) {
                      $q2->where('email', 'like', '%'.$email.'%');
                  })
                  ->orWhereHas('redeemer', function ($q3) use ($email) {
                      $q3->where('email', 'like', '%'.$email.'%');
                  });
            });
        }

        $giftcards = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        $courses = Course::orderBy('name')->get(['id','name']);

        return view('admin.giftcards.index', compact('giftcards', 'courses'));
    }

    public function show(GiftCard $giftcard)
    {
        $giftcard->load(['course', 'buyer', 'redeemer']);
        $payment = null;
        if ($giftcard->stripe_session_id) {
            $payment = Payment::where('stripe_session_id', $giftcard->stripe_session_id)->first();
        }
        if (!$payment && $giftcard->stripe_payment_intent_id) {
            $payment = Payment::where('stripe_payment_intent_id', $giftcard->stripe_payment_intent_id)->first();
        }
        return view('admin.giftcards.show', compact('giftcard', 'payment'));
    }
}
