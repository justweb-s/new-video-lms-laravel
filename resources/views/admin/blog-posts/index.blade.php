<x-layouts.admin>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Articoli del Blog
            </h2>
            <a href="{{ route('admin.blog-posts.create') }}" class="inline-flex items-center bg-primary hover:bg-primary/90 text-white font-semibold px-4 py-2 rounded">
                Nuovo Articolo
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.blog-posts.index') }}" class="flex flex-wrap items-end gap-3 mb-6">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Cerca</label>
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Titolo, contenuto..." class="border rounded px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Stato</label>
                            <select name="status" class="border rounded px-3 py-2 text-sm">
                                <option value="">Tutti</option>
                                @foreach(['draft' => 'Bozza', 'scheduled' => 'Programmato', 'published' => 'Pubblicato'] as $key => $label)
                                    <option value="{{ $key }}" @selected(request('status')===$key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="inline-flex items-center gap-2 text-sm">
                                <input type="checkbox" name="pinned" value="1" @checked(request('pinned')) /> Pinned
                            </label>
                        </div>
                        <div>
                            <button class="bg-gray-800 hover:bg-gray-900 text-white font-semibold px-4 py-2 rounded">Filtra</button>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titolo</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stato</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pubblicato</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pinned</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Autore</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($posts as $post)
                                    <tr>
                                        <td class="px-4 py-3 text-sm">
                                            <div class="font-medium text-gray-900">{{ $post->title }}</div>
                                            <div class="text-gray-500 text-xs">/{{ $post->slug }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @php
                                                $badge = match($post->status) {
                                                    'published' => 'bg-green-100 text-green-800',
                                                    'scheduled' => 'bg-blue-100 text-blue-800',
                                                    default => 'bg-gray-100 text-gray-800',
                                                };
                                                $label = match($post->status) {
                                                    'published' => 'Pubblicato',
                                                    'scheduled' => 'Programmato',
                                                    default => 'Bozza',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">{{ $label }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ optional($post->published_at)->format('d/m/Y H:i') ?: '—' }}</td>
                                        <td class="px-4 py-3 text-sm">{!! $post->pinned ? '<span class="text-green-600">Sì</span>' : '<span class="text-gray-400">No</span>' !!}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $post->admin->full_name ?? '—' }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('admin.blog-posts.edit', $post) }}" class="text-sm bg-gray-800 hover:bg-gray-900 text-white px-3 py-1.5 rounded">Modifica</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">Nessun articolo trovato.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $posts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
