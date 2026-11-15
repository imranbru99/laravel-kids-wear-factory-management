@extends('site.layout')
@php $locale = app()->getLocale(); $title = __('kidswear.nav.journal').' · '.kidswear('name'); @endphp
@section('content')
<section class="mx-auto max-w-3xl px-5 py-12">
    <h1 class="display text-5xl">{{ __('kidswear.nav.journal') }}</h1>
    <div class="mt-8 space-y-6">
        @foreach ($posts as $post)
            <a href="{{ route('blog.show', [$locale, $post]) }}" class="block rounded-3xl bg-white p-6 ring-1 ring-sand">
                <p class="text-xs uppercase tracking-wider text-ink/45">{{ $post->published_at?->toFormattedDateString() }}</p>
                <h2 class="display mt-2 text-3xl">{{ $post->localizedTitle() }}</h2>
                <p class="mt-2 text-ink/70">{{ $post->excerpt }}</p>
            </a>
        @endforeach
    </div>
    <div class="mt-6">{{ $posts->links() }}</div>
</section>
@endsection
