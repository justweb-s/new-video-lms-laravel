@extends('layouts.public')

@section('title', $post->meta_title ?: $post->title)
@section('meta_description', $post->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($post->excerpt ?: $post->content), 155))
@section('canonical', route('blog.show', $post->slug))
@section('meta_image', $post->cover_image ?: asset('images/favicon-studio.png'))

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-6">
        <a href="{{ route('blog.index') }}" class="inline-flex items-center text-sm text-primary hover:text-primary/80">
            ← Torna al Blog
        </a>
    </div>

    <article>
        <header class="mb-6">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900">{{ $post->title }}</h1>
            <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-gray-500">
                @if($post->published_at)
                    <time datetime="{{ $post->published_at->toIso8601String() }}">{{ $post->published_at->format('d M Y') }}</time>
                @endif
                @if($post->reading_time)
                    <span>• {{ $post->reading_time }} min</span>
                @endif
                @if($post->admin)
                    <span>• {{ $post->admin->full_name }}</span>
                @endif
            </div>
        </header>

        @if($post->cover_image)
            <div class="mb-8">
                <img src="{{ $post->cover_image }}" alt="{{ $post->title }}" class="w-full rounded-lg shadow border">
            </div>
        @endif

        @if($post->excerpt)
            <p class="text-lg text-gray-700 mb-6">{{ $post->excerpt }}</p>
        @endif

        <div class="prose prose-lg max-w-none">
            {!! $post->content !!}
        </div>

        @if(is_array($post->tags) && count($post->tags))
            <div class="mt-8 flex flex-wrap gap-2">
                @foreach($post->tags as $tag)
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-semibold">#{{ $tag }}</span>
                @endforeach
            </div>
        @endif
    </article>
</div>
@endsection
