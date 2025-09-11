<x-layouts.admin>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Impostazioni SEO Pagine Statiche
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.settings.seo.update') }}">
                        @csrf

                        <div class="space-y-8">
                            @foreach($pages as $key => $label)
                                <fieldset class="border-t border-gray-200 pt-6">
                                    <legend class="text-lg font-medium text-gray-900">{{ $label }}</legend>
                                    <div class="mt-4 grid grid-cols-1 gap-y-6">
                                        <div>
                                            <label for="seo_{{ $key }}_title" class="block text-sm font-medium text-gray-700">Meta Titolo</label>
                                            <input type="text" name="seo[{{ $key }}][title]" id="seo_{{ $key }}_title" 
                                                   value="{{ old('seo.'.$key.'.title', $settings[$key]->meta_title ?? '') }}" 
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                            <p class="mt-1 text-sm text-gray-500">Se vuoto, verrà usato un titolo di default.</p>
                                        </div>
                                        <div>
                                            <label for="seo_{{ $key }}_description" class="block text-sm font-medium text-gray-700">Meta Descrizione</label>
                                            <textarea name="seo[{{ $key }}][description]" id="seo_{{ $key }}_description" rows="3" 
                                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">{{ old('seo.'.$key.'.description', $settings[$key]->meta_description ?? '') }}</textarea>
                                            <p class="mt-1 text-sm text-gray-500">Se vuota, verrà usata una descrizione di default.</p>
                                        </div>
                                    </div>
                                </fieldset>
                            @endforeach
                        </div>

                        <div class="flex items-center justify-end mt-8">
                            <button type="submit" class="bg-primary hover:bg-primary/90 text-white font-bold py-2 px-4 rounded">
                                Salva Impostazioni
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
