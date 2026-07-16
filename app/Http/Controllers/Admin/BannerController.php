<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->latest()->paginate(10);
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'badge' => 'nullable|string|max:255',
            'highlights' => 'nullable|string',
            'buy_url' => 'nullable|url',
            'detail_url' => 'nullable|url',
            'sort_order' => 'nullable|integer',
        ]);

        $banner = new Banner();
        $banner->title = $request->title;
        $banner->description = $request->description;
        $banner->badge = $request->badge;
        $banner->buy_url = $request->buy_url;
        $banner->detail_url = $request->detail_url;
        $banner->is_active = $request->has('is_active');
        $banner->sort_order = $request->sort_order ?? 0;

        if ($request->filled('highlights')) {
            // Convert comma separated string to array
            $highlightsArray = array_map('trim', explode(',', $request->highlights));
            $banner->highlights = array_filter($highlightsArray);
        } else {
            $banner->highlights = null;
        }

        if ($request->hasFile('image')) {
            $banner->image = $request->file('image')->store('banners', 'public');
        }

        $banner->save();

        return redirect()->route('admin.banners.index')->with('success', 'Thêm Banner thành công!');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'badge' => 'nullable|string|max:255',
            'highlights' => 'nullable|string',
            'buy_url' => 'nullable|url',
            'detail_url' => 'nullable|url',
            'sort_order' => 'nullable|integer',
        ]);

        $banner->title = $request->title;
        $banner->description = $request->description;
        $banner->badge = $request->badge;
        $banner->buy_url = $request->buy_url;
        $banner->detail_url = $request->detail_url;
        $banner->is_active = $request->has('is_active');
        $banner->sort_order = $request->sort_order ?? 0;

        if ($request->filled('highlights')) {
            $highlightsArray = array_map('trim', explode(',', $request->highlights));
            $banner->highlights = array_filter($highlightsArray);
        } else {
            $banner->highlights = null;
        }

        if ($request->hasFile('image')) {
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $banner->image = $request->file('image')->store('banners', 'public');
        }

        $banner->save();

        return redirect()->route('admin.banners.index')->with('success', 'Cập nhật Banner thành công!');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Xoá Banner thành công!');
    }
}
