@extends('site.layout')
@php
    $locale = app()->getLocale();
    $title = $style->meta_title ?: $style->localizedName();
    $description = $style->meta_description ?: $style->excerpt;
    $jsonLd = $style->json_ld ?: [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $style->name,
        'sku' => $style->style_code,
        'description' => $style->excerpt,
        'brand' => kidswear('name'),
        'category' => $style->category?->name,
    ];
@endphp
@section('content')
<article class="mx-auto grid max-w-6xl gap-10 px-5 py-12 md:grid-cols-2">
    <div class="swatch min-h-80 rounded-[2rem]" style="--swatch-a: {{ $style->colorways->first()->hex ?? '#e15b3a' }}; --swatch-b: #f4efe6"></div>
    <div>
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-leaf">{{ $style->style_code }} · {{ $style->category?->name }}</p>
        <h1 class="display mt-3 text-5xl">{{ $style->localizedName() }}</h1>
        <p class="mt-4 text-lg text-ink/75">{{ $style->localizedDescription() }}</p>
        <dl class="mt-6 grid grid-cols-2 gap-3 text-sm">
            <div class="rounded-2xl bg-cream p-3"><dt class="text-ink/50">Age</dt><dd>{{ $style->ageGroup?->label }}</dd></div>
            <div class="rounded-2xl bg-cream p-3"><dt class="text-ink/50">Season</dt><dd>{{ $style->season?->name }}</dd></div>
            <div class="rounded-2xl bg-cream p-3"><dt class="text-ink/50">Fabric</dt><dd>{{ $style->fabric ?: '—' }}</dd></div>
            <div class="rounded-2xl bg-cream p-3"><dt class="text-ink/50">GSM</dt><dd>{{ $style->gsm ?: '—' }}</dd></div>
        </dl>
        <div class="mt-6 flex flex-wrap gap-2">
            @foreach ($style->colorways as $color)
                <span class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-sm ring-1 ring-sand">
                    <i class="h-3 w-3 rounded-full" style="background: {{ $color->hex ?: '#ccc' }}"></i>{{ $color->name }}
                </span>
            @endforeach
        </div>
        @if ($style->sizeCharts->first())
            <h2 class="display mt-8 text-2xl">Size chart (cm)</h2>
            <table class="mt-3 w-full text-left text-sm">
                <thead><tr class="text-ink/50"><th class="py-2">Size</th><th>Chest</th><th>Length</th><th>Waist</th></tr></thead>
                <tbody>
                @foreach ($style->sizeCharts->first()->rows as $row)
                    <tr class="border-t border-sand">
                        <td class="py-2 font-semibold">{{ $row->size_code }}</td>
                        <td>{{ $row->measurements['chest'] ?? '—' }}</td>
                        <td>{{ $row->measurements['length'] ?? '—' }}</td>
                        <td>{{ $row->measurements['waist'] ?? '—' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
        <form method="post" action="{{ route('inquiry.store', $locale) }}" class="mt-8 space-y-3 rounded-3xl bg-ink p-5 text-cream">
            @csrf
            <input type="hidden" name="type" value="buyer">
            <input type="hidden" name="style_id" value="{{ $style->id }}">
            <h2 class="display text-2xl">{{ __('kidswear.nav.inquire') }}</h2>
            <input name="name" required placeholder="Name" class="w-full rounded-xl bg-white/10 px-3 py-2">
            <input name="company" required placeholder="Company" class="w-full rounded-xl bg-white/10 px-3 py-2">
            <input name="email" type="email" required placeholder="Email" class="w-full rounded-xl bg-white/10 px-3 py-2">
            <input name="country" placeholder="Country" class="w-full rounded-xl bg-white/10 px-3 py-2">
            <textarea name="message" required placeholder="Quantity, age run, delivery window" class="w-full rounded-xl bg-white/10 px-3 py-2"></textarea>
            <button class="rounded-full bg-coral px-4 py-2 text-sm font-semibold text-white">Send</button>
        </form>
    </div>
</article>
@endsection
