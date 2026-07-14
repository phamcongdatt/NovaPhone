@extends('layouts.app')
@section('title', $post->title . ' — NovaPhone')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:py-16">
    <nav class="mb-6 flex text-sm text-slate-400" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="inline-flex items-center hover:text-white transition-colors">
                    <svg class="mr-2.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Trang chủ
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="h-4 w-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <a href="{{ route('posts.index') }}" class="ml-1 hover:text-white transition-colors md:ml-2">Tin tức</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="h-4 w-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <span class="ml-1 line-clamp-1 max-w-[200px] md:ml-2">{{ $post->title }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <article class="rounded-3xl border border-white/5 bg-[#0b1523] p-6 shadow-xl sm:p-10">
        <header class="mb-8 border-b border-white/10 pb-8">
            <h1 class="mb-4 text-3xl font-extrabold leading-tight text-white sm:text-4xl lg:text-5xl">
                {{ $post->title }}
            </h1>
            
            @if($post->summary)
                <p class="mb-6 text-lg font-medium leading-relaxed text-slate-300">
                    {{ $post->summary }}
                </p>
            @endif

            <div class="flex flex-wrap items-center gap-6 text-sm text-slate-400">
                <div class="flex items-center gap-2">
                    <div class="flex size-8 items-center justify-center rounded-full bg-brand-500/20 text-brand-400">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                    </div>
                    <span class="font-medium">{{ $post->author->name ?? 'Admin' }}</span>
                </div>
                
                <div class="flex items-center gap-2">
                    <svg class="size-5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    <span>{{ $post->published_at ? $post->published_at->format('d/m/Y H:i') : '' }}</span>
                </div>
            </div>
        </header>

        @if($post->thumbnail)
            <figure class="mb-10 overflow-hidden rounded-2xl bg-black/20">
                <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->title }}" class="w-full object-cover">
            </figure>
        @endif

        <div class="prose prose-invert max-w-none prose-img:rounded-xl prose-a:text-brand-400 hover:prose-a:text-brand-300">
            {!! $post->content !!}
        </div>
    </article>
</div>
@endsection
