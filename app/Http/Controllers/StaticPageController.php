<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Setting;
use App\Mail\ContactMessage;

class StaticPageController extends Controller
{
    public function home()
    {
        $featuredCourses = Course::where('is_active', true)
                                   ->where('is_featured', true)
                                   ->take(4)
                                   ->get();

        return view('static.home', compact('featuredCourses'));
    }

    public function about()
    {
        return view('static.about');
    }

    public function contact()
    {
        $phone = Setting::get('contact.phone', '+39 350 014 9957');
        $email = Setting::get('contact.email', 'info@emyworkout.it');
        $address = Setting::get('contact.address', 'Via Alfredo Catalani 38A, 09128 Cagliari CA');
        $mapEmbed = Setting::get('contact.map_embed', 'https://maps.google.com/maps?q=Via%20Alfredo%20Catalani%C2%A038A%2C%2009128%20Cagliari%20CA&z=16&hl=it&t=m&output=embed&iwloc=near');

        return view('static.contact', compact('phone','email','address','mapEmbed'));
    }

    public function submitContact(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:120'],
            'email' => ['required','email','max:190'],
            'phone' => ['nullable','string','max:50'],
            'subject' => ['nullable','string','max:190'],
            'message' => ['required','string','max:5000'],
        ]);

        $data['ip'] = $request->ip();

        $recipient = Setting::get('contact.recipient_email', config('mail.from.address') ?? env('MAIL_FROM_ADDRESS'));

        try {
            Mail::to($recipient)->send(new ContactMessage($data));
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['email' => 'Errore durante l\'invio del messaggio. Riprova più tardi.']);
        }

        return back()->with('status', 'Messaggio inviato correttamente! Ti risponderemo al più presto.');
    }

    public function workoutOnline()
    {
        // Recupera tutti i corsi dal catalogo
        $courses = Course::where('is_active', true)->get();
        
        return view('static.workout-online', compact('courses'));
    }

    public function workoutInStudio()
    {
        return view('static.workout-in-studio');
    }

    public function bookAConsultation()
    {
        return view('static.book-a-consultation');
    }

    public function submitBookAConsultation(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:120'],
            'cognome' => ['required','string','max:120'],
            'email' => ['required','email','max:190'],
            'phone' => ['required','string','max:50'],
            'message' => ['nullable','string','max:5000'],
        ]);

        $data['ip'] = $request->ip();

        $recipient = Setting::get('contact.recipient_email', config('mail.from.address') ?? env('MAIL_FROM_ADDRESS'));

        try {
            Mail::to($recipient)->send(new ContactMessage($data));
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['email' => 'Errore durante l\'invio del messaggio. Riprova più tardi.']);
        }

        return back()->with('status', 'Messaggio inviato correttamente! Ti risponderemo al più presto.');
    }
}
