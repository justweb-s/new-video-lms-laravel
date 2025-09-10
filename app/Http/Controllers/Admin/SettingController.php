<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function editContact()
    {
        $data = [
            'phone' => Setting::get('contact.phone', ''),
            'email' => Setting::get('contact.email', ''),
            'address' => Setting::get('contact.address', ''),
            'map_embed' => Setting::get('contact.map_embed', ''),
            'recipient_email' => Setting::get('contact.recipient_email', config('mail.from.address')),
        ];

        return view('admin.settings.contact', $data);
    }

    public function updateContact(Request $request)
    {
        $validated = $request->validate([
            'phone' => ['nullable','string','max:120'],
            'email' => ['nullable','email','max:190'],
            'address' => ['nullable','string','max:255'],
            'map_embed' => ['nullable','string','max:2000'],
            'recipient_email' => ['required','email','max:190'],
        ]);

        // If full iframe was pasted, try to extract src
        if (!empty($validated['map_embed']) && str_contains($validated['map_embed'], '<iframe')) {
            if (preg_match('/src="([^"]+)"/i', $validated['map_embed'], $m)) {
                $validated['map_embed'] = $m[1];
            }
        }

        foreach ($validated as $key => $value) {
            Setting::set("contact.$key", $value);
        }

        return back()->with('status', 'Impostazioni contatti aggiornate con successo.');
    }
}
