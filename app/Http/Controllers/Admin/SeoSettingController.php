<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoSetting;
use Illuminate\Http\Request;

class SeoSettingController extends Controller
{
    public function edit()
    {
        $pages = [
            'static.home' => 'Home Page',
            'static.about' => 'Chi Sono',
            'static.contact' => 'Contatti',
            'static.workout-online' => 'Workout Online',
            'static.workout-in-studio' => 'Workout in Studio',
            'catalog.index' => 'Catalogo Corsi',
        ];

        // Ensure a setting exists for each page, without creating duplicates.
        foreach (array_keys($pages) as $key) {
            SeoSetting::firstOrCreate(['page_key' => $key]);
        }

        // Fetch all settings, now that we're sure they exist.
        $settings = SeoSetting::all()->keyBy('page_key');

        return view('admin.settings.seo', compact('pages', 'settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'seo.*.title' => 'nullable|string|max:255',
            'seo.*.description' => 'nullable|string|max:500',
        ]);

        foreach ($validated['seo'] as $pageKey => $values) {
            SeoSetting::updateOrCreate(
                ['page_key' => $pageKey],
                ['meta_title' => $values['title'], 'meta_description' => $values['description']]
            );
        }

        return redirect()->route('admin.settings.seo.edit')
            ->with('success', 'Impostazioni SEO aggiornate con successo!');
    }
}
