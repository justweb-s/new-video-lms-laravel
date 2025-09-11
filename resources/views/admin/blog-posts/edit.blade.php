<x-layouts.admin>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Modifica Articolo
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.blog-posts.index') }}" class="inline-flex items-center px-4 py-2 rounded bg-gray-100 hover:bg-gray-200 text-gray-800">Torna alla lista</a>
                <form method="POST" action="{{ route('admin.blog-posts.destroy', $post) }}" onsubmit="return confirm('Sei sicuro di voler eliminare questo articolo?');">
                    @csrf
                    @method('DELETE')
                    <button class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded">Elimina</button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @include('admin.blog-posts._form', ['post' => $post])
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
