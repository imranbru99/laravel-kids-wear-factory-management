@extends('site.layout')
@php $title = __('kidswear.nav.gallery').' · '.kidswear('name'); @endphp
@section('content')
<section class="mx-auto max-w-6xl px-5 py-12">
    <h1 class="display text-5xl">{{ __('kidswear.nav.gallery') }}</h1>
    <div class="mt-8 grid gap-4 sm:grid-cols-2">
        @foreach ($items as $item)
            <figure class="overflow-hidden rounded-[1.6rem] bg-cream">
                <div class="swatch h-56" style="--swatch-a: {{ ['#e15b3a','#21584a','#e7dcc8','#1c2430'][$loop->index % 4] }}; --swatch-b: #fbf8f2"></div>
                <figcaption class="p-5">
                    <h2 class="display text-2xl">{{ $item->title }}</h2>
                    <p class="text-sm text-ink/70">{{ $item->caption }}</p>
                </figcaption>
            </figure>
        @endforeach
    </div>
</section>
@endsection
