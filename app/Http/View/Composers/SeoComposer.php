<?php

namespace App\Http\View\Composers;

use App\Models\SeoSetting;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

class SeoComposer
{
    protected $seoSettings;

    public function __construct()
    {
        $this->seoSettings = Cache::rememberForever('seo_settings', function () {
            return SeoSetting::all()->keyBy('page_key');
        });
    }

    public function compose(View $view)
    {
        $routeName = request()->route()->getName();

        $seoData = $this->seoSettings->get($routeName);

        $defaultTitle = $seoData->meta_title ?? null;
        $defaultDescription = $seoData->meta_description ?? null;

        $view->with('defaultSeoTitle', $defaultTitle)
             ->with('defaultSeoDescription', $defaultDescription);
    }
}
