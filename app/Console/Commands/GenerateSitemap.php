<?php

namespace App\Console\Commands;

use App\Models\Course;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route as RouteFacade;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera il file public/sitemap.xml con le pagine pubbliche del sito';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Generazione sitemap in corso...');

        $sitemap = Sitemap::create()
            ->add(Url::create(route('static.home'))->setPriority(1.0))
            ->add(Url::create(route('static.about'))->setPriority(0.7))
            ->add(Url::create(route('static.contact'))->setPriority(0.7))
            ->add(Url::create(route('static.workout-online'))->setPriority(0.7))
            ->add(Url::create(route('static.workout-in-studio'))->setPriority(0.7))
            ->add(Url::create(route('privacy-policy'))->setPriority(0.3))
            ->add(Url::create(route('cookie-policy'))->setPriority(0.3))
            ->add(Url::create(route('catalog.index'))->setPriority(0.8));

        // Corsi del catalogo
        foreach (Course::query()->orderBy('id')->cursor() as $course) {
            $sitemap->add(
                Url::create(route('catalog.show', $course))
                    ->setLastModificationDate($course->updated_at ?? now())
                    ->setPriority(0.8)
            );
        }

        // Gift Cards (solo indice; le pagine di dettaglio dipendono dalla configurazione del corso)
        if (RouteFacade::has('giftcards.index')) {
            $sitemap->add(Url::create(route('giftcards.index'))->setPriority(0.6));
        }

        $path = public_path('sitemap.xml');
        $sitemap->writeToFile($path);

        $this->info("Sitemap generata: {$path}");
        return self::SUCCESS;
    }
}
