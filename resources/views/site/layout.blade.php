<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? kidswear('name') }}</title>
    <meta name="description" content="{{ $description ?? kidswear(app()->getLocale() === 'bn' ? 'tagline_bn' : 'tagline') }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="alternate" hreflang="en" href="{{ preg_replace('#/(en|bn)(/|$)#', '/en$2', url()->current()) }}">
    <link rel="alternate" hreflang="bn" href="{{ preg_replace('#/(en|bn)(/|$)#', '/bn$2', url()->current()) }}">
    <meta property="og:title" content="{{ $title ?? kidswear('name') }}">
    <meta property="og:description" content="{{ $description ?? kidswear('tagline') }}">
    <meta property="og:type" content="website">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|fraunces:500,620,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @isset($jsonLd)
        <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @endisset
</head>
<body class="min-h-screen font-sans antialiased">
@php $locale = app()->getLocale(); $other = $locale === 'bn' ? 'en' : 'bn'; @endphp
<header class="sticky top-0 z-30 border-b border-sand/70 bg-paper/80 backdrop-blur-xl" x-data="{ open: false }">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-5 py-3.5">
        <a href="{{ route('home', $locale) }}" class="display text-xl tracking-tight text-ink">রংধনু <span class="text-coral">Kids</span></a>
        <nav class="hidden items-center gap-7 text-[13px] font-medium tracking-wide text-ink/70 md:flex">
            <a class="transition hover:text-ink" href="{{ route('about', $locale) }}">{{ __('kidswear.nav.about') }}</a>
            <a class="transition hover:text-ink" href="{{ route('process', $locale) }}">{{ __('kidswear.nav.process') }}</a>
            <a class="transition hover:text-ink" href="{{ route('certifications', $locale) }}">{{ __('kidswear.nav.certifications') }}</a>
            <a class="transition hover:text-ink" href="{{ route('catalog', $locale) }}">{{ __('kidswear.nav.styles') }}</a>
            <a class="transition hover:text-ink" href="{{ route('gallery', $locale) }}">{{ __('kidswear.nav.gallery') }}</a>
            <a class="transition hover:text-ink" href="{{ route('blog', $locale) }}">{{ __('kidswear.nav.journal') }}</a>
        </nav>
        <div class="flex items-center gap-2.5">
            <a href="{{ preg_replace('#/(en|bn)(/|$)#', '/'.$other.'$2', url()->current()) }}" class="rounded-full border border-sand bg-white/70 px-3 py-1.5 text-xs font-semibold tracking-wide">{{ $other === 'bn' ? 'বাংলা' : 'EN' }}</a>
            <a href="{{ route('contact', $locale) }}" class="hidden rounded-full bg-ink px-4 py-2 text-sm font-semibold text-cream shadow-sm transition hover:bg-leaf sm:inline">{{ __('kidswear.nav.inquire') }}</a>
            <button type="button" class="rounded-full border border-sand px-3 py-1.5 md:hidden" @click="open = !open" aria-label="Menu">☰</button>
        </div>
    </div>
    <div class="space-y-3 border-t border-sand px-5 py-4 text-sm md:hidden" x-show="open" x-cloak>
        <a class="block" href="{{ route('about', $locale) }}">{{ __('kidswear.nav.about') }}</a>
        <a class="block" href="{{ route('process', $locale) }}">{{ __('kidswear.nav.process') }}</a>
        <a class="block" href="{{ route('catalog', $locale) }}">{{ __('kidswear.nav.styles') }}</a>
        <a class="block" href="{{ route('gallery', $locale) }}">{{ __('kidswear.nav.gallery') }}</a>
        <a class="block" href="{{ route('blog', $locale) }}">{{ __('kidswear.nav.journal') }}</a>
        <a class="block" href="{{ route('contact', $locale) }}">{{ __('kidswear.nav.contact') }}</a>
    </div>
</header>
<main>
    @if (session('status'))
        <div class="mx-auto mt-4 max-w-6xl rounded-2xl bg-mist px-4 py-3 text-sm text-leaf">{{ session('status') }}</div>
    @endif
    {{ $slot ?? '' }}
    @yield('content')
</main>
<footer class="mt-20 border-t border-sand bg-ink text-cream">
    <div class="mx-auto grid max-w-6xl gap-8 px-5 py-12 md:grid-cols-3">
        <div>
            <p class="display text-2xl">{{ $locale === 'bn' ? kidswear('name_bn') : kidswear('name') }}</p>
            <p class="mt-3 text-sm text-cream/70">{{ $locale === 'bn' ? kidswear('address_bn') : kidswear('address') }}</p>
        </div>
        <div class="text-sm text-cream/80">
            <p>{{ kidswear('phone') }}</p>
            <p>{{ kidswear('email') }}</p>
            <p class="mt-3">{{ kidswear('capacity') }}</p>
        </div>
        <div class="text-sm">
            <a class="block text-cream/80" href="{{ route('login') }}">Factory desk</a>
            <a class="block text-cream/80" href="{{ route('sitemap') }}">Sitemap</a>
        </div>
    </div>
</footer>
<style>[x-cloak]{display:none}</style>
</body>
</html>
