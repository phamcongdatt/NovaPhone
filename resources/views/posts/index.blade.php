@extends('layouts.app')

@section('title', 'Tin công nghệ - NovaPhone')

@section('content')
    <div class="space-y-8 pb-4 sm:space-y-10">
        <section class="relative overflow-hidden border-y border-[#e7e6e3] bg-[#f5f5f7] py-10 sm:py-14">
            <div class="mx-auto w-full">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#0875e1]">NovaPhone Journal</p>
                <div class="mt-3 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-3xl">
                        <h1 class="text-3xl font-semibold tracking-[-0.045em] text-[#1d1d1f] sm:text-5xl">Tin công nghệ</h1>
                        <p class="mt-3 max-w-2xl text-base leading-7 text-[#6e6e73] sm:text-lg">
                            Cập nhật những thông tin, xu hướng và sản phẩm công nghệ mới từ NovaPhone.
                        </p>
                    </div>
                    <a href="#danh-sach-bai-viet" class="inline-flex w-fit items-center gap-2 text-sm font-semibold text-[#0066cc] transition hover:text-[#004f9e] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0066cc] focus-visible:ring-offset-4">
                        Khám phá bài viết
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M3 8h9M8.5 3.5 13 8l-4.5 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
            </div>
        </section>

        <section id="danh-sach-bai-viet" class="mx-auto w-full scroll-mt-24">
            <div class="space-y-5 sm:space-y-6">
                @forelse ($posts as $post)
                    <article class="group overflow-hidden border border-[#e5e5e7] bg-white shadow-[0_12px_32px_rgba(0,0,0,0.05)] transition duration-300 hover:shadow-[0_18px_42px_rgba(0,0,0,0.09)] md:grid md:grid-cols-[minmax(300px,42%)_minmax(0,1fr)]">
                        <a href="{{ route('posts.show', $post->slug) }}" class="relative block h-60 overflow-hidden bg-[#ededf0] sm:h-72 md:h-auto md:min-h-[290px]" aria-label="Đọc bài viết: {{ $post->title }}">
                            @if ($post->thumbnail)
                                <img
                                    src="{{ str_starts_with($post->thumbnail, 'http') ? $post->thumbnail : (str_starts_with($post->thumbnail, 'images/') || str_starts_with($post->thumbnail, 'storage/') ? asset($post->thumbnail) : asset('storage/' . $post->thumbnail)) }}"
                                    alt="{{ $post->title }}"
                                    class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-[1.025]"
                                    @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif
                                    decoding="async"
                                >
                            @else
                                <div class="absolute inset-0 flex items-center justify-center bg-[radial-gradient(circle_at_65%_20%,rgba(0,102,204,.2),transparent_34%),linear-gradient(135deg,#1d1d1f,#4b4b50)]">
                                    <span class="text-sm font-semibold tracking-[0.16em] text-white/80">NOVAPHONE JOURNAL</span>
                                </div>
                            @endif
                        </a>

                        <div class="flex min-w-0 flex-col justify-center p-6 sm:p-8 lg:p-10">
                            @if ($post->published_at)
                                <time datetime="{{ $post->published_at->toDateString() }}" class="text-xs font-semibold uppercase tracking-[0.14em] text-[#6e6e73]">
                                    {{ $post->published_at->format('d/m/Y') }}
                                </time>
                            @endif
                            <h2 class="mt-3 text-xl font-semibold tracking-[-0.03em] text-[#1d1d1f] sm:text-2xl lg:text-3xl">
                                <a href="{{ route('posts.show', $post->slug) }}" class="transition hover:text-[#0066cc] focus:outline-none focus-visible:rounded-sm focus-visible:ring-2 focus-visible:ring-[#0066cc]">
                                    {{ $post->title }}
                                </a>
                            </h2>
                            @if ($post->summary)
                                <p class="mt-4 max-w-3xl text-sm leading-6 text-[#6e6e73] sm:text-base sm:leading-7">{{ $post->summary }}</p>
                            @endif
                            <a href="{{ route('posts.show', $post->slug) }}" class="mt-6 inline-flex w-fit items-center gap-2 text-sm font-semibold text-[#0066cc] transition hover:text-[#004f9e] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0066cc] focus-visible:ring-offset-4">
                                Đọc bài viết
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <path d="M3 8h9M8.5 3.5 13 8l-4.5 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="border border-dashed border-[#d2d2d7] bg-[#f5f5f7] px-6 py-16 text-center sm:py-24">
                        <svg class="mx-auto h-9 w-9 text-[#8e8e93]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 5h14v14H5zM8 15l2.5-3 2 2 1.5-2 2 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <h2 class="mt-4 text-xl font-semibold text-[#1d1d1f]">Chưa có tin công nghệ</h2>
                        <p class="mt-2 text-sm text-[#6e6e73]">Các bài viết đã xuất bản sẽ xuất hiện tại đây.</p>
                    </div>
                @endforelse
            </div>

            @if ($posts->hasPages())
                <div class="mt-10 border-t border-[#e5e5e7] pt-6">
                    {{ $posts->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
