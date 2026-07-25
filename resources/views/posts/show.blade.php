@extends('layouts.app')
@section('title', $post->title . ' - NovaPhone')

@section('content')
<section class="rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-[0_10px_35px_rgba(0,0,0,.04)]">
    <a href="{{ route('posts.index') }}" class="text-sm text-[#777] hover:text-black">← Tin công nghệ</a>
    <h1 class="mt-4 text-3xl font-bold">{{ $post->title }}</h1>
    <p class="mt-2 text-sm text-[#7f7f7f]">
        {{ $post->published_at?->format('d/m/Y') }}
    </p>
    @if ($post->thumbnail)
        <img src="{{ str_starts_with($post->thumbnail, 'http') ? $post->thumbnail : (str_starts_with($post->thumbnail, 'images/') || str_starts_with($post->thumbnail, 'storage/') ? asset($post->thumbnail) : asset('storage/' . $post->thumbnail)) }}" class="mt-5 aspect-[16/8] w-full rounded-[24px] object-cover" alt="{{ $post->title }}" fetchpriority="high" decoding="async">
    @endif
    @if ($post->summary)
        <p class="mt-6 text-lg leading-8 text-[#555]">{{ $post->summary }}</p>
    @endif
    <div class="prose prose-neutral mt-6 max-w-none">
        {!! nl2br(e($post->content)) !!}
    </div>
</section>
@endsection
