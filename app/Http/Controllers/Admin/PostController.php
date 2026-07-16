<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['author', 'category'])->latest();

        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $posts = $query->paginate(15);

        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = \App\Models\PostCategory::where('is_active', true)->get();
        return view('admin.posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'post_category_id' => 'nullable|exists:post_categories,id',
        ]);

        $post = new Post();
        $post->title = $request->title;
        $post->slug = Str::slug($request->title) . '-' . time();
        $post->summary = $request->summary;
        $post->content = $request->content;
        $post->post_category_id = $request->post_category_id;
        $post->is_published = $request->has('is_published');
        if ($post->is_published) {
            $post->published_at = now();
        }
        $post->author_id = auth()->id();

        if ($request->hasFile('thumbnail')) {
            $imageName = time() . '_' . uniqid() . '.' . $request->file('thumbnail')->extension();
            $request->file('thumbnail')->move(public_path('images/posts'), $imageName);
            $post->thumbnail = 'images/posts/' . $imageName;
        }

        $post->save();

        return redirect()->route('admin.posts.index')->with('success', 'Thêm bài viết thành công!');
    }

    public function edit(Post $post)
    {
        $categories = \App\Models\PostCategory::where('is_active', true)->get();
        return view('admin.posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'post_category_id' => 'nullable|exists:post_categories,id',
        ]);

        $post->title = $request->title;
        if ($post->isDirty('title')) {
            $post->slug = Str::slug($request->title) . '-' . time();
        }
        $post->summary = $request->summary;
        $post->content = $request->content;
        $post->post_category_id = $request->post_category_id;
        
        $wasPublished = $post->is_published;
        $post->is_published = $request->has('is_published');
        
        if (!$wasPublished && $post->is_published) {
            $post->published_at = now();
        } elseif (!$post->is_published) {
            $post->published_at = null;
        }

        if ($request->hasFile('thumbnail')) {
            if ($post->thumbnail && file_exists(public_path($post->thumbnail))) {
                unlink(public_path($post->thumbnail));
            }
            $imageName = time() . '_' . uniqid() . '.' . $request->file('thumbnail')->extension();
            $request->file('thumbnail')->move(public_path('images/posts'), $imageName);
            $post->thumbnail = 'images/posts/' . $imageName;
        }

        $post->save();

        return redirect()->route('admin.posts.index')->with('success', 'Cập nhật bài viết thành công!');
    }

    public function destroy(Post $post)
    {
        if ($post->thumbnail && file_exists(public_path($post->thumbnail))) {
            unlink(public_path($post->thumbnail));
        }
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Xóa bài viết thành công!');
    }
}
