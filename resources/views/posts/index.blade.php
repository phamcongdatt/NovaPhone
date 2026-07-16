@extends('layouts.app')
@section('title', 'Tin Tức Công Nghệ — NovaPhone')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:py-16">
    <div class="mb-10 text-center">
        <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">Tin Tức Công Nghệ</h1>
        <p class="mt-3 text-lg text-slate-400">Cập nhật những thông tin mới nhất về thị trường di động và công nghệ.</p>
    </div>

    @if($posts->count() > 0)
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($posts as $post)
                <a href="{{ route('posts.show', $post->slug) }}" class="group flex flex-col overflow-hidden rounded-2xl border border-white/5 bg-[#0b1523] shadow-lg transition-all hover:-translate-y-1 hover:border-brand-500/30 hover:shadow-xl hover:shadow-brand-500/10">
                    <div class="relative aspect-video overflow-hidden bg-black/20">
                        <img src="{{ $post->thumbnail ? (str_starts_with($post->thumbnail, 'images/') ? asset($post->thumbnail) : asset('storage/' . $post->thumbnail)) : asset('images/placeholder.svg') }}" alt="{{ $post->title }}" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
                    </div>
                    <div class="flex flex-1 flex-col p-5">
                        <div class="mb-3 flex items-center gap-3 text-xs text-slate-500">
                            <span class="flex items-center gap-1.5">
                                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                {{ $post->published_at ? $post->published_at->format('d/m/Y') : '' }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                                {{ $post->author->name ?? 'Admin' }}
                            </span>
                        </div>
                        <h2 class="line-clamp-2 text-base font-bold leading-snug text-white transition-colors group-hover:text-brand-400">
                            {{ $post->title }}
                        </h2>
                        <p class="mt-2 line-clamp-3 text-sm text-slate-400">
                            {{ $post->summary ?? Str::limit(strip_tags($post->content), 120) }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-12 flex justify-center">
            {{ $posts->links('pagination::tailwind') }}
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="mb-4 flex size-20 items-center justify-center rounded-full bg-white/5 text-slate-500">
                <svg class="size-10" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
            </div>
            <h3 class="text-xl font-bold text-white">Chưa có bài viết nào</h3>
            <p class="mt-2 text-slate-400">Chúng tôi đang cập nhật các thông tin mới nhất. Vui lòng quay lại sau.</p>
        </div>
    @endif
</div>
@endsection
