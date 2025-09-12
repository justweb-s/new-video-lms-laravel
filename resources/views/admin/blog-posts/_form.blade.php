@php
    $isEdit = $post && $post->exists;
    $action = $isEdit ? route('admin.blog-posts.update', $post) : route('admin.blog-posts.store');
    $method = $isEdit ? 'PUT' : 'POST';
    $publishedAtValue = old('published_at', optional($post->published_at)->format('Y-m-d\TH:i'));
    $tagsValue = old('tags', is_array($post->tags) ? implode(', ', $post->tags) : ($post->tags ?? ''));
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Titolo</label>
                <input type="text" name="title" value="{{ old('title', $post->title) }}" class="mt-1 block w-full border rounded px-3 py-2" required>
                @error('title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $post->slug) }}" class="mt-1 block w-full border rounded px-3 py-2" placeholder="lascia vuoto per generarlo dal titolo">
                @error('slug')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Estratto</label>
                <textarea name="excerpt" rows="3" class="mt-1 block w-full border rounded px-3 py-2">{{ old('excerpt', $post->excerpt) }}</textarea>
                @error('excerpt')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="content" class="block text-sm font-medium text-gray-700">Contenuto</label>
                <input id="content" type="hidden" name="content" value="{{ old('content', $post->content) }}">
                <trix-editor input="content" class="trix-content mt-1 block w-full border rounded px-3 py-2"></trix-editor>
                @error('content')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Stato</label>
                <select name="status" class="mt-1 block w-full border rounded px-3 py-2">
                    @foreach(['draft' => 'Bozza', 'scheduled' => 'Programmato', 'published' => 'Pubblicato'] as $k => $label)
                        <option value="{{ $k }}" @selected(old('status', $post->status)===$k)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Data pubblicazione</label>
                <input type="datetime-local" name="published_at" value="{{ $publishedAtValue }}" class="mt-1 block w-full border rounded px-3 py-2">
                @error('published_at')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Cover</label>
                @if($post->cover_image)
                    <img src="{{ $post->cover_image }}" alt="Cover" class="w-full aspect-video object-cover rounded border">
                @endif
                <input type="file" name="cover_image_upload" accept="image/*" class="mt-1 block w-full border rounded px-3 py-2">
                @error('cover_image_upload')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" name="pinned" value="1" @checked(old('pinned', $post->pinned))> In evidenza (pinned)
                </label>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Tag (separati da virgola)</label>
                <input type="text" name="tags" value="{{ $tagsValue }}" class="mt-1 block w-full border rounded px-3 py-2">
                @error('tags')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="pt-2 border-t">
                <p class="text-xs uppercase text-gray-500 mb-2">SEO</p>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Meta Title</label>
                        <input type="text" name="meta_title" value="{{ old('meta_title', $post->meta_title) }}" class="mt-1 block w-full border rounded px-3 py-2">
                        @error('meta_title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Meta Description</label>
                        <textarea name="meta_description" rows="3" class="mt-1 block w-full border rounded px-3 py-2">{{ old('meta_description', $post->meta_description) }}</textarea>
                        @error('meta_description')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-start gap-2">
        <button class="bg-primary hover:bg-primary/90 text-white font-semibold px-4 py-2 rounded">Salva</button>
        @if($isEdit && $post->status === 'published')
            <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="inline-flex items-center px-4 py-2 rounded bg-gray-100 hover:bg-gray-200 text-gray-800">Vedi pubblico</a>
        @endif
    </div>
</form>
