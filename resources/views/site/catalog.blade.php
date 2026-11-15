@extends('site.layout')
@php $locale = app()->getLocale(); $title = __('kidswear.nav.styles').' · '.kidswear('name'); @endphp
@section('content')
<section class="mx-auto max-w-6xl px-5 py-12">
    <h1 class="display text-5xl">{{ __('kidswear.nav.styles') }}</h1>
    <form class="mt-6 grid gap-3 rounded-3xl bg-cream p-4 md:grid-cols-4" method="get">
        <input name="q" value="{{ request('q') }}" placeholder="{{ $locale === 'bn' ? 'স্টাইল বা কোড' : 'Style or code' }}" class="rounded-2xl border border-sand bg-white px-3 py-2">
        <select name="category" class="rounded-2xl border border-sand bg-white px-3 py-2">
            <option value="">{{ $locale === 'bn' ? 'সব ক্যাটাগরি' : 'All categories' }}</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $locale === 'bn' && $category->name_bn ? $category->name_bn : $category->name }}</option>
            @endforeach
        </select>
        <select name="age" class="rounded-2xl border border-sand bg-white px-3 py-2">
            <option value="">{{ $locale === 'bn' ? 'সব বয়স' : 'All ages' }}</option>
            @foreach ($ages as $age)
                <option value="{{ $age->id }}" @selected(request('age') == $age->id)>{{ $locale === 'bn' && $age->label_bn ? $age->label_bn : $age->label }}</option>
            @endforeach
        </select>
        <button class="rounded-2xl bg-ink px-4 py-2 text-sm font-semibold text-cream">{{ $locale === 'bn' ? 'ফিল্টার' : 'Filter' }}</button>
    </form>
    <div class="mt-8 grid gap-5 md:grid-cols-3">
        @forelse ($styles as $style)
            <a href="{{ route('styles.show', [$locale, $style]) }}" class="rounded-[1.6rem] bg-white p-4 ring-1 ring-sand">
                <div class="swatch h-40 rounded-2xl" style="--swatch-a: {{ $style->colorways->first()->hex ?? '#e7dcc8' }}; --swatch-b: #1c2430"></div>
                <p class="mt-4 text-xs uppercase tracking-wider text-ink/45">{{ $style->style_code }}</p>
                <h2 class="display text-2xl">{{ $style->localizedName() }}</h2>
                <p class="text-sm text-ink/60">{{ $style->ageGroup?->label }} · {{ $style->season?->name }}</p>
            </a>
        @empty
            <p class="text-ink/60">No published styles match those filters.</p>
        @endforelse
    </div>
    <div class="mt-8">{{ $styles->links() }}</div>
</section>
@endsection
