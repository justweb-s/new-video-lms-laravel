@extends('layouts.public')

@section('content')
<div class="bg-white">
    <!-- Hero section -->
    <div class="relative bg-gray-900">
        <div aria-hidden="true" class="absolute inset-0 overflow-hidden">
            <img src="{{ asset('images/emy-workout-2.jpg') }}" alt="" class="w-full h-full object-center object-cover">
        </div>
        <div aria-hidden="true" class="absolute inset-0 bg-gray-900 opacity-50"></div>
        <div class="relative max-w-3xl mx-auto py-32 px-6 flex flex-col items-center text-center sm:py-64 lg:px-0">
            <h1 class="text-4xl font-extrabold tracking-tight text-white lg:text-6xl">Non Parliamo di Allenamento, Parliamo di Te.</h1>
            <p class="mt-4 text-xl text-white">Un percorso pensato su misura per te. Insieme possiamo costruire un piano e uno stile di vita che ti porta verso il successo.</p>
            <a href="{{ route('courses.index') }}" class="mt-8 inline-block bg-white border border-transparent rounded-md py-3 px-8 text-base font-medium text-gray-900 hover:bg-gray-100">Allenamento Adattato</a>
        </div>
    </div>

    <main>
        <!-- Category section -->
        <section aria-labelledby="category-heading" class="pt-24 sm:pt-32 xl:max-w-7xl xl:mx-auto xl:px-8">
            <div class="px-4 sm:px-6 sm:flex sm:items-center sm:justify-between lg:px-8 xl:px-0">
                <h2 id="category-heading" class="text-2xl font-extrabold tracking-tight text-gray-900">I percorsi più richiesti</h2>
                <a href="{{ route('courses.index') }}" class="hidden text-sm font-semibold text-indigo-600 hover:text-indigo-500 sm:block">Tutti i percorsi<span aria-hidden="true"> &rarr;</span></a>
            </div>

            <div class="mt-4 flow-root">
                <div class="-my-2">
                    <div class="box-content py-2 relative h-80 overflow-x-auto xl:overflow-visible">
                        <div class="absolute min-w-screen-xl px-4 flex space-x-8 sm:px-6 lg:px-8 xl:relative xl:px-0 xl:space-x-0 xl:grid xl:grid-cols-4 xl:gap-x-8">
                            <a href="{{ route('courses.index') }}" class="relative w-56 h-80 rounded-lg p-6 flex flex-col overflow-hidden hover:opacity-75 xl:w-auto">
                                <span aria-hidden="true" class="absolute inset-0">
                                    <img src="{{ asset('images/allenamento-1.jpg') }}" alt="" class="w-full h-full object-center object-cover">
                                </span>
                                <span aria-hidden="true" class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-gray-800 opacity-50"></span>
                                <span class="relative mt-auto text-center text-xl font-bold text-white">Dimagrimento</span>
                            </a>

                            <a href="{{ route('courses.index') }}" class="relative w-56 h-80 rounded-lg p-6 flex flex-col overflow-hidden hover:opacity-75 xl:w-auto">
                                <span aria-hidden="true" class="absolute inset-0">
                                    <img src="{{ asset('images/allenamento-2.jpg') }}" alt="" class="w-full h-full object-center object-cover">
                                </span>
                                <span aria-hidden="true" class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-gray-800 opacity-50"></span>
                                <span class="relative mt-auto text-center text-xl font-bold text-white">Glutei</span>
                            </a>

                            <a href="{{ route('courses.index') }}" class="relative w-56 h-80 rounded-lg p-6 flex flex-col overflow-hidden hover:opacity-75 xl:w-auto">
                                <span aria-hidden="true" class="absolute inset-0">
                                    <img src="{{ asset('images/allenamento-3.jpg') }}" alt="" class="w-full h-full object-center object-cover">
                                </span>
                                <span aria-hidden="true" class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-gray-800 opacity-50"></span>
                                <span class="relative mt-auto text-center text-xl font-bold text-white">Tonificazione</span>
                            </a>

                            <a href="{{ route('courses.index') }}" class="relative w-56 h-80 rounded-lg p-6 flex flex-col overflow-hidden hover:opacity-75 xl:w-auto">
                                <span aria-hidden="true" class="absolute inset-0">
                                    <img src="{{ asset('images/allenamento-4.jpg') }}" alt="" class="w-full h-full object-center object-cover">
                                </span>
                                <span aria-hidden="true" class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-gray-800 opacity-50"></span>
                                <span class="relative mt-auto text-center text-xl font-bold text-white">Personalizzato</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 px-4 sm:hidden">
                <a href="{{ route('courses.index') }}" class="block text-sm font-semibold text-indigo-600 hover:text-indigo-500">Tutti i percorsi<span aria-hidden="true"> &rarr;</span></a>
            </div>
        </section>

        <!-- Featured section -->
        <section aria-labelledby="social-impact-heading" class="max-w-7xl mx-auto pt-24 px-4 sm:pt-32 sm:px-6 lg:px-8">
            <div class="relative rounded-lg overflow-hidden">
                <div class="absolute inset-0">
                    <img src="{{ asset('images/emy-workout-2.jpg') }}" alt="" class="w-full h-full object-center object-cover">
                </div>
                <div class="relative bg-gray-900 bg-opacity-75 py-32 px-6 sm:py-40 sm:px-12 lg:px-16">
                    <div class="relative max-w-3xl mx-auto flex flex-col items-center text-center">
                        <h2 id="social-impact-heading" class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                            <span class="block sm:inline">Workout Online</span>
                        </h2>
                        <p class="mt-3 text-xl text-white">Acquista subito il tuo workout online</p>
                        <a href="{{ route('static.workout-online') }}" class="mt-8 w-full block bg-white border border-transparent rounded-md py-3 px-8 text-base font-medium text-gray-900 hover:bg-gray-100 sm:w-auto">Inizia Ora</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Collection section -->
        <section aria-labelledby="collection-heading" class="max-w-xl mx-auto pt-24 px-4 sm:pt-32 sm:px-6 lg:max-w-7xl lg:px-8">
            <h2 id="collection-heading" class="text-2xl font-extrabold tracking-tight text-gray-900">I Miei Allenamenti</h2>
            <p class="mt-4 text-base text-gray-500">Alcuni scatti dei miei allenamenti in studio e online.</p>

            <div class="mt-10 space-y-12 lg:space-y-0 lg:grid lg:grid-cols-3 lg:gap-x-8">
                <a href="{{ route('static.workout-in-studio') }}" class="group block">
                    <div aria-hidden="true" class="aspect-w-3 aspect-h-2 rounded-lg overflow-hidden group-hover:opacity-75 lg:aspect-w-5 lg:aspect-h-6">
                        <img src="{{ asset('images/allenamento-1.jpg') }}" alt="" class="w-full h-full object-center object-cover">
                    </div>
                    <h3 class="mt-4 text-base font-semibold text-gray-900">Workout in studio</h3>
                    <p class="mt-2 text-sm text-gray-500">Allenamento personalizzato e adattato</p>
                </a>

                <a href="{{ route('static.workout-online') }}" class="group block">
                    <div aria-hidden="true" class="aspect-w-3 aspect-h-2 rounded-lg overflow-hidden group-hover:opacity-75 lg:aspect-w-5 lg:aspect-h-6">
                        <img src="{{ asset('images/allenamento-2.jpg') }}" alt="" class="w-full h-full object-center object-cover">
                    </div>
                    <h3 class="mt-4 text-base font-semibold text-gray-900">Workout online</h3>
                    <p class="mt-2 text-sm text-gray-500">Allenati con me da casa</p>
                </a>

                <a href="{{ route('courses.index') }}" class="group block">
                    <div aria-hidden="true" class="aspect-w-3 aspect-h-2 rounded-lg overflow-hidden group-hover:opacity-75 lg:aspect-w-5 lg:aspect-h-6">
                        <img src="{{ asset('images/allenamento-3.jpg') }}" alt="" class="w-full h-full object-center object-cover">
                    </div>
                    <h3 class="mt-4 text-base font-semibold text-gray-900">Allenamento funzionale</h3>
                    <p class="mt-2 text-sm text-gray-500">Migliora la tua forza e resistenza</p>
                </a>
            </div>
        </section>

        <!-- Testimonials section -->
        <section aria-labelledby="testimonial-heading" class="relative max-w-7xl mx-auto py-24 px-4 sm:px-6 lg:py-32 lg:px-8">
            <div class="max-w-2xl mx-auto lg:max-w-none">
                <h2 id="testimonial-heading" class="text-2xl font-extrabold tracking-tight text-gray-900">Cosa Dicono i Miei Clienti</h2>

                <div class="mt-16 space-y-16 lg:space-y-0 lg:grid lg:grid-cols-3 lg:gap-x-8">
                    <blockquote class="sm:flex lg:block">
                        <svg width="24" height="18" viewBox="0 0 24 18" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="flex-shrink-0 text-gray-300">
                            <path d="M0 18v-6l6-6v6h6v6H0zM12 18v-6l6-6v6h6v6h-12z" stroke="currentColor" stroke-width="2" fill="currentColor" />
                        </svg>
                        <div class="mt-8 sm:mt-0 sm:ml-6 lg:mt-8 lg:ml-0">
                            <p class="text-lg text-gray-600">Frequento questo studio da 2 anni e non ho intenzione di cambiare. Gli allenamenti sono personalizzati ed efficaci, ma il vero punto forte è la personal trainer, Emy. Persona meravigliosa, energica e positiva.</p>
                            <cite class="mt-4 block font-semibold not-italic text-gray-900">Arianna Corti</cite>
                        </div>
                    </blockquote>

                    <blockquote class="sm:flex lg:block">
                        <svg width="24" height="18" viewBox="0 0 24 18" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="flex-shrink-0 text-gray-300">
                            <path d="M0 18v-6l6-6v6h6v6H0zM12 18v-6l6-6v6h6v6h-12z" stroke="currentColor" stroke-width="2" fill="currentColor" />
                        </svg>
                        <div class="mt-8 sm:mt-0 sm:ml-6 lg:mt-8 lg:ml-0">
                            <p class="text-lg text-gray-600">Mi alleno da poco con Emy, ma ho trovato subito moltissimi benefici alle mie problematiche grazie ai suoi preziosi consigli ,alla sua preparazione e alla sua attenzione ad ogni piccolo particolare ,super consigliata !</p>
                            <cite class="mt-4 block font-semibold not-italic text-gray-900">Marzia Angius</cite>
                        </div>
                    </blockquote>

                    <blockquote class="sm:flex lg:block">
                        <svg width="24" height="18" viewBox="0 0 24 18" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="flex-shrink-0 text-gray-300">
                            <path d="M0 18v-6l6-6v6h6v6H0zM12 18v-6l6-6v6h6v6h-12z" stroke="currentColor" stroke-width="2" fill="currentColor" />
                        </svg>
                        <div class="mt-8 sm:mt-0 sm:ml-6 lg:mt-8 lg:ml-0">
                            <p class="text-lg text-gray-600">Ho avuto e ho la fortuna di lavorare con Emy, una personal trainer straordinaria sotto ogni aspetto. Non solo è incredibilmente preparata e professionale, ma ciò che la rende davvero unica è la sua profonda sensibilità e empatia verso le persone…</p>
                            <cite class="mt-4 block font-semibold not-italic text-gray-900">Giorgia Lopez</cite>
                        </div>
                    </blockquote>
                </div>
            </div>
        </section>
    </main>
</div>
@endsection
