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
        $categories = PostCategory::withCount('posts')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->string('search')->toString());
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.post-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.post-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $validated['slug'] = $this->uniqueSlug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        PostCategory::create($validated);

        return redirect()
            ->route('admin.post-categories.index')
            ->with('success', 'Thêm danh mục bài viết thành công.');
    }

    public function edit(PostCategory $postCategory)
    {
        return view('admin.post-categories.edit', compact('postCategory'));
    }

    public function update(Request $request, PostCategory $postCategory)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        if ($validated['name'] !== $postCategory->name) {
            $validated['slug'] = $this->uniqueSlug($validated['name'], $postCategory->id);
        }

        $validated['is_active'] = $request->boolean('is_active');
        $postCategory->update($validated);

        return redirect()
            ->route('admin.post-categories.index')
            ->with('success', 'Cập nhật danh mục bài viết thành công.');
    }

    public function destroy(PostCategory $postCategory)
    {
        $postCategory->delete();

        return redirect()
            ->route('admin.post-categories.index')
            ->with('success', 'Xóa danh mục bài viết thành công.');
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'danh-muc';
        $slug = $base;
        $counter = 1;

        while (PostCategory::where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
