<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MyApp') — Xác thực</title>

    {{-- Tailwind CSS CDN (dùng Vite + tailwind cho production) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50:  '#eef2ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .input-field {
            @apply w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm
                   focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                   transition duration-150;
        }
        .btn-primary {
            @apply w-full py-2.5 px-4 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium
                   rounded-lg transition duration-150 focus:outline-none focus:ring-2
                   focus:ring-offset-2 focus:ring-brand-500;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-brand-50 to-indigo-100 flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-brand-600 rounded-2xl mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">MyApp</h1>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-xl p-8">

            {{-- Flash messages --}}
            @if (session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('status'))
                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Dev reset link (xóa khi production) --}}
            @if (session('dev_link'))
                <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-xs text-yellow-800">
                    <strong>[DEV]</strong> Link reset:
                    <a href="{{ session('dev_link') }}" class="underline break-all">{{ session('dev_link') }}</a>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

</body>
</html>