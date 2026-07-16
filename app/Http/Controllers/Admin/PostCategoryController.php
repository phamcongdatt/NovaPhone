<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = PostCategory::withCount('posts')->latest();

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->paginate(15);

        return view('admin.post-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.post-categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = new PostCategory();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name) . '-' . time();
        $category->description = $request->description;
        $category->is_active = $request->has('is_active');
        $category->save();

        return redirect()->route('admin.post-categories.index')->with('success', 'Thêm danh mục bài viết thành công!');
    }

    public function edit(PostCategory $postCategory)
    {
        return view('admin.post-categories.edit', compact('postCategory'));
    }

    public function update(Request $request, PostCategory $postCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $postCategory->name = $request->name;
        if ($postCategory->isDirty('name')) {
            $postCategory->slug = Str::slug($request->name) . '-' . time();
        }
        $postCategory->description = $request->description;
        $postCategory->is_active = $request->has('is_active');
        $postCategory->save();

        return redirect()->route('admin.post-categories.index')->with('success', 'Cập nhật danh mục bài viết thành công!');
    }

    public function destroy(PostCategory $postCategory)
    {
        if ($postCategory->posts()->exists()) {
            return back()->withErrors(['message' => 'Không thể xoá danh mục đã có bài viết!']);
        }
        $postCategory->delete();

        return redirect()->route('admin.post-categories.index')->with('success', 'Xóa danh mục bài viết thành công!');
    }
}
