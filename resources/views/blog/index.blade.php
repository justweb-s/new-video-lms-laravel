@extends('layouts.public')

@section('title', 'Blog')
@section('meta_description', 'Articoli, consigli e approfondimenti su allenamento, benessere e programmi Emy Workout.')
@section('canonical', route('blog.index'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <h1 class="text-3xl font-extrabold text-primary">Blog</h1>
        <form method="GET" action="{{ route('blog.index') }}" class="flex items-center gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cerca articoli..." class="border rounded px-3 py-2 text-sm w-64">
            <button class="bg-primary text-white px-4 py-2 rounded text-sm">Cerca</button>
        </form>
    </div>

    @if($posts->count() === 0)
        <div class="bg-white border rounded p-6 text-center text-gray-600">Nessun articolo disponibile.</div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($posts as $post)
                <article class="bg-white rounded-lg shadow-sm border overflow-hidden flex flex-col">
                    <a href="{{ route('blog.show', $post->slug) }}" class="block">
                        <div class="aspect-[16/9] bg-gray-100 overflow-hidden">
                            @if($post->cover_image)
                                <img src="{{ $post->cover_image }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">Nessuna immagine</div>
                            @endif
                        </div>
                    </a>
                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                            <span>{{ optional($post->published_at)->format('d M Y') }}</span>
                            @if($post->reading_time)
                                <span>{{ $post->reading_time }} min</span>
                            @endif
                        </div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">
                            <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-primary">{{ $post->title }}</a>
                        </h2>
                        <p class="text-gray-600 text-sm line-clamp-3">{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 140) }}</p>
                        <div class="mt-4">
                            <a href="{{ route('blog.show', $post->slug) }}" class="inline-flex items-center text-primary hover:text-primary/80 text-sm">Leggi di più →</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    @endif
</div>
@endsection
