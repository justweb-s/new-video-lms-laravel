<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BlogPostRequest;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogPostController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::query();

        if ($request->filled('q')) {
            $query->search($request->string('q'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->boolean('pinned')) {
            $query->where('pinned', true);
        }

        $posts = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('admin.blog-posts.index', compact('posts'));
    }

    public function create()
    {
        $post = new BlogPost([ 'status' => 'draft' ]);
        return view('admin.blog-posts.create', compact('post'));
    }

    public function store(BlogPostRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('cover_image_upload')) {
            $path = $request->file('cover_image_upload')->store('blog-covers', 'public');
            $data['cover_image'] = Storage::disk('public')->url($path);
        }

        $data['admin_id'] = auth('admin')->id();
        $data['pinned'] = $request->boolean('pinned');

        $post = BlogPost::create($data);

        return redirect()->route('admin.blog-posts.edit', $post)->with('success', 'Articolo creato con successo.');
    }

    public function edit(BlogPost $blog_post)
    {
        $post = $blog_post;
        return view('admin.blog-posts.edit', compact('post'));
    }

    public function update(BlogPostRequest $request, BlogPost $blog_post)
    {
        $data = $request->validated();

        if ($request->hasFile('cover_image_upload')) {
            $path = $request->file('cover_image_upload')->store('blog-covers', 'public');
            $data['cover_image'] = Storage::disk('public')->url($path);
        }

        $data['pinned'] = $request->boolean('pinned');

        $blog_post->update($data);

        return redirect()->route('admin.blog-posts.edit', $blog_post)->with('success', 'Articolo aggiornato con successo.');
    }

    public function destroy(BlogPost $blog_post)
    {
        $blog_post->delete();
        return redirect()->route('admin.blog-posts.index')->with('success', 'Articolo eliminato.');
    }

    public function show(BlogPost $blog_post)
    {
        // In admin, reindirizziamo a modifica per praticità
        return redirect()->route('admin.blog-posts.edit', $blog_post);
    }
}
