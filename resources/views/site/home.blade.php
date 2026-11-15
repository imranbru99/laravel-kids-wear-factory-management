@extends('site.layout')
@php
    $locale = app()->getLocale();
    $title = kidswear($locale === 'bn' ? 'name_bn' : 'name');
    $description = kidswear($locale === 'bn' ? 'tagline_bn' : 'tagline');
    $featured = $styles->first();
    $rest = $styles->skip(1);
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => kidswear('name'),
        'url' => url('/'.$locale),
        'email' => kidswear('email'),
        'telephone' => kidswear('phone'),
        'address' => ['@type' => 'PostalAddress', 'streetAddress' => kidswear('address'), 'addressCountry' => 'BD'],
    ];
    $steps = $locale === 'bn'
        ? [
            ['০১', 'টেক প্যাক', 'সাইজ চার্ট, রং ও বোম এক স্টাইল কোডে।'],
            ['০২', 'মার্কার ও কাট', 'সাইজ রেশিও থেকে লেয়ার, বান্ডিল টিকিটসহ।'],
            ['০৩', 'সেলাই ও চেক', 'লাইন আউটপুট ও ইনলাইন ডিফেক্ট একসাথে।'],
            ['০৪', 'প্যাক ও জাহাজ', 'প্যাকিং লিস্ট ও কমার্শিয়াল ইনভয়েস।'],
        ]
        : [
            ['01', 'Tech pack', 'Size chart, colorway, and BOM on one style code.'],
            ['02', 'Marker & cut', 'A size ratio becomes the lay, with a bundle ticket.'],
            ['03', 'Sew & check', 'Line output and inline defects stay on the same order.'],
            ['04', 'Pack & ship', 'Packing list and commercial invoice from the size run.'],
        ];
@endphp
@section('content')
<section class="hero-stage relative overflow-hidden">
    <div class="grain pointer-events-none absolute inset-0"></div>
    <div class="relative mx-auto grid max-w-6xl items-center gap-12 px-5 pb-16 pt-12 lg:grid-cols-[1.15fr_.85fr] lg:pb-20 lg:pt-16">
        <div>
            <p class="inline-flex items-center gap-2 rounded-full border border-sand bg-white/80 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-leaf">
                <span class="h-1.5 w-1.5 rounded-full bg-coral"></span>
                {{ $locale === 'bn' ? 'গাজীপুর' : 'Gazipur' }} · {{ kidswear('established') }}
            </p>
            <h1 class="display mt-6 max-w-3xl text-[3.15rem] leading-[0.95] tracking-[-0.03em] text-ink sm:text-7xl lg:text-[5.4rem]">
                @if ($locale === 'bn')
                    নরম কাপড়।<br>স্পষ্ট সাইজ।<br><span class="text-coral">সময়মতো</span> জাহাজ।
                @else
                    Soft cloth.<br>Clear sizes.<br><span class="italic text-coral">On time.</span>
                @endif
            </h1>
            <p class="mt-6 max-w-md text-lg leading-relaxed text-ink/70">{{ $description }}</p>
            <div class="mt-8 flex flex-wrap items-center gap-3">
                <a href="{{ route('catalog', $locale) }}" class="rounded-full bg-ink px-6 py-3.5 text-sm font-semibold text-cream shadow-lg shadow-ink/10 transition hover:bg-leaf">{{ __('kidswear.nav.styles') }}</a>
                <a href="{{ route('process', $locale) }}" class="rounded-full border border-ink/10 bg-white/80 px-6 py-3.5 text-sm font-semibold transition hover:border-ink/30">{{ __('kidswear.nav.process') }}</a>
            </div>
            <dl class="mt-10 grid max-w-lg grid-cols-3 gap-4 border-t border-sand/80 pt-6">
                @foreach ([
                    [$locale === 'bn' ? 'লাইন' : 'Lines', kidswear('lines')],
                    [$locale === 'bn' ? 'কর্মী' : 'Workers', locale_number(kidswear('workers'))],
                    [$locale === 'bn' ? 'ক্ষমতা' : 'Capacity', '420k'],
                ] as [$label, $value])
                    <div>
                        <dt class="text-[11px] font-semibold uppercase tracking-[0.16em] text-ink/40">{{ $label }}</dt>
                        <dd class="display mt-1 text-3xl tracking-tight">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        <div class="relative mx-auto w-full max-w-md lg:max-w-none">
            <div class="look-float overflow-hidden rounded-[2rem] bg-white ring-1 ring-black/5">
                <div class="swatch relative h-80 sm:h-[26rem]" style="--swatch-a: {{ $featured?->colorways->first()->hex ?? '#e15b3a' }}; --swatch-b: #21584a">
                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-ink/70 to-transparent p-6 text-cream">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-cream/70">{{ $featured?->style_code ?? 'SS26' }} · {{ $featured?->season?->code ?? 'SS26' }}</p>
                        <p class="display mt-1 text-3xl">{{ $featured ? $featured->localizedName() : ($locale === 'bn' ? 'স্প্রিং কালেকশন' : 'Spring collection') }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-3 divide-x divide-sand">
                    @foreach ($styles->take(3) as $swatch)
                        <a href="{{ route('styles.show', [$locale, $swatch]) }}" class="group px-3 py-4">
                            <span class="mb-2 block h-8 rounded-full" style="background: {{ $swatch->colorways->first()->hex ?? '#e15b3a' }}"></span>
                            <span class="block text-[11px] font-semibold uppercase tracking-wider text-ink/45">{{ $swatch->style_code }}</span>
                            <span class="mt-0.5 block truncate text-sm font-medium group-hover:text-coral">{{ $swatch->localizedName() }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="look-float absolute -left-3 top-8 hidden rounded-2xl bg-white/95 px-4 py-3 ring-1 ring-sand backdrop-blur sm:block">
                <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-ink/40">{{ $locale === 'bn' ? 'উইকএন্ড' : 'Weekend' }}</p>
                <p class="display text-lg">{{ $locale === 'bn' ? 'শুক্র–শনি' : 'Fri – Sat' }}</p>
            </div>
            <div class="look-float absolute -right-2 bottom-24 hidden rounded-2xl bg-ink px-4 py-3 text-cream sm:block">
                <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-cream/50">{{ $locale === 'bn' ? 'বয়স' : 'Ages' }}</p>
                <p class="display text-lg">0–12Y</p>
            </div>
        </div>
    </div>
</section>

@if ($certs->isNotEmpty())
<div class="overflow-hidden border-y border-sand bg-white/70 py-4">
    <div class="marquee-track flex w-max gap-10 px-6">
        @foreach ($certs->concat($certs) as $cert)
            <p class="flex items-center gap-3 text-sm font-semibold tracking-[0.18em] text-ink/55 uppercase">
                <span class="h-1.5 w-1.5 rounded-full bg-coral"></span>{{ $cert->name }}
            </p>
        @endforeach
    </div>
</div>
@endif

<section class="mx-auto max-w-6xl px-5 py-16 lg:py-20">
    <div class="mb-8 flex items-end justify-between gap-4">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-leaf">{{ $locale === 'bn' ? 'এই সিজন' : 'This season' }}</p>
            <h2 class="display mt-2 text-4xl tracking-tight sm:text-5xl">{{ $locale === 'bn' ? 'রুম থেকে রপ্তানি' : 'From the floor' }}</h2>
        </div>
        <a href="{{ route('catalog', $locale) }}" class="shrink-0 text-sm font-semibold text-leaf">{{ __('kidswear.nav.styles') }} →</a>
    </div>
    @if ($featured)
        <div class="grid gap-4 lg:grid-cols-12">
            <a href="{{ route('styles.show', [$locale, $featured]) }}" class="group overflow-hidden rounded-[1.8rem] bg-white ring-1 ring-sand lg:col-span-7">
                <div class="swatch h-72 sm:h-96" style="--swatch-a: {{ $featured->colorways->first()->hex ?? '#e15b3a' }}; --swatch-b: #1c2430"></div>
                <div class="flex items-end justify-between gap-4 p-6">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-ink/40">{{ $featured->style_code }} · {{ $featured->ageGroup?->label }}</p>
                        <h3 class="display mt-2 text-3xl group-hover:text-coral">{{ $featured->localizedName() }}</h3>
                    </div>
                    <p class="text-sm text-ink/60">{{ $featured->category?->name }}</p>
                </div>
            </a>
            <div class="grid gap-4 sm:grid-cols-2 lg:col-span-5 lg:grid-cols-1">
                @foreach ($rest as $style)
                    <a href="{{ route('styles.show', [$locale, $style]) }}" class="group grid grid-cols-[7.5rem_1fr] overflow-hidden rounded-[1.4rem] bg-white ring-1 ring-sand">
                        <div class="swatch min-h-28" style="--swatch-a: {{ $style->colorways->first()->hex ?? '#21584a' }}; --swatch-b: #f4efe6"></div>
                        <div class="flex flex-col justify-center px-4 py-3">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-ink/40">{{ $style->style_code }}</p>
                            <h3 class="display mt-1 text-xl leading-tight group-hover:text-coral">{{ $style->localizedName() }}</h3>
                            <p class="mt-1 text-sm text-ink/60">{{ $style->ageGroup?->label }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</section>

<section class="mx-auto max-w-6xl px-5 pb-8">
    <div class="overflow-hidden rounded-[2rem] bg-ink text-cream">
        <div class="grid lg:grid-cols-[0.8fr_1.2fr]">
            <div class="border-b border-white/10 p-8 lg:border-b-0 lg:border-r lg:p-10">
                <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-cream/45">{{ $locale === 'bn' ? 'কারখানার ছন্দ' : 'The floor' }}</p>
                <h2 class="display mt-3 text-4xl leading-tight">{{ $locale === 'bn' ? 'কাগজের বোর্ড নয়। একটা অর্ডার।' : 'One order. No paper board.' }}</h2>
                <a href="{{ route('contact', $locale) }}" class="mt-8 inline-flex rounded-full bg-coral px-5 py-3 text-sm font-semibold text-white">{{ __('kidswear.nav.inquire') }}</a>
            </div>
            <ol class="grid sm:grid-cols-2">
                @foreach ($steps as $step)
                    <li class="border-b border-white/10 p-6 last:border-b-0 sm:[&:nth-child(odd)]:border-r">
                        <p class="display text-2xl text-coral">{{ $step[0] }}</p>
                        <p class="mt-3 text-lg font-semibold">{{ $step[1] }}</p>
                        <p class="mt-2 text-sm leading-relaxed text-cream/65">{{ $step[2] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
</section>

@if ($posts->isNotEmpty())
<section class="mx-auto max-w-6xl px-5 py-16">
    <div class="mb-8 flex items-end justify-between">
        <h2 class="display text-4xl">{{ __('kidswear.nav.journal') }}</h2>
        <a href="{{ route('blog', $locale) }}" class="text-sm font-semibold text-leaf">{{ $locale === 'bn' ? 'সব লেখা' : 'All notes' }} →</a>
    </div>
    <div class="grid gap-4 md:grid-cols-3">
        @foreach ($posts as $post)
            <a href="{{ route('blog.show', [$locale, $post]) }}" class="group rounded-[1.6rem] bg-cream p-6 ring-1 ring-sand transition hover:-translate-y-0.5">
                <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-ink/40">{{ $post->published_at?->format('d M Y') }}</p>
                <h3 class="display mt-3 text-2xl leading-snug group-hover:text-coral">{{ $post->localizedTitle() }}</h3>
                <p class="mt-3 line-clamp-3 text-sm leading-relaxed text-ink/65">{{ $post->excerpt }}</p>
            </a>
        @endforeach
    </div>
</section>
@endif

@if ($certs->isNotEmpty())
<section class="mx-auto max-w-6xl px-5 pb-6">
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($certs as $cert)
            <a href="{{ route('certifications', $locale) }}" class="rounded-[1.4rem] border border-sand bg-white px-5 py-5">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-coral">{{ $cert->code }}</p>
                <p class="display mt-2 text-2xl">{{ $cert->name }}</p>
                <p class="mt-2 line-clamp-2 text-sm text-ink/60">{{ $locale === 'bn' && $cert->summary_bn ? $cert->summary_bn : $cert->summary }}</p>
            </a>
        @endforeach
    </div>
</section>
@endif
@endsection
