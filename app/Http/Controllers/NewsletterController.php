<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:newsletters,email',
        ]);

        if ($validator->fails()) {
            return redirect('/#newsletter-section')
                        ->withErrors($validator)
                        ->withInput();
        }

        Newsletter::create([
            'email' => $request->email,
        ]);

        return redirect('/#newsletter-section')->with('success', 'Grazie per esserti iscritto alla nostra newsletter!');
    }
}
