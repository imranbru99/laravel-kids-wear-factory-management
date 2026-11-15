@extends('site.layout')
@php $title = $post->localizedTitle(); $description = $post->excerpt; @endphp
@section('content')
<article class="mx-auto max-w-3xl px-5 py-14">
    <p class="text-xs uppercase tracking-wider text-leaf">{{ $post->published_at?->toFormattedDateString() }}</p>
    <h1 class="display mt-3 text-5xl">{{ $post->localizedTitle() }}</h1>
    <div class="prose mt-6 max-w-none whitespace-pre-line text-lg leading-relaxed text-ink/80">{{ $post->localizedBody() }}</div>
</article>
@endsection
