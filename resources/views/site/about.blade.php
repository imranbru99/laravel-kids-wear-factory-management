@extends('site.layout')
@php $locale = app()->getLocale(); $title = __('kidswear.nav.about').' · '.kidswear('name'); @endphp
@section('content')
<article class="mx-auto max-w-3xl px-5 py-16">
    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-leaf">{{ kidswear('established') }}</p>
    <h1 class="display mt-3 text-5xl">{{ $locale === 'bn' ? 'গাজীপুরের একটি শিশু পোশাক কারখানা' : 'A kids wear factory in Gazipur' }}</h1>
    <p class="mt-6 text-lg leading-relaxed text-ink/80">
        {{ $locale === 'bn'
            ? 'রংধনু কিডস ওয়্যার শিশুদের টি-শার্ট, রম্পার, সেট, ড্রেস ও প্যান্ট তৈরি করে। সাইজ রান ০-৩ মাস থেকে ১২ বছর পর্যন্ত একই টেক প্যাকে থাকে, যাতে ক্রেতা ও লাইন একই মাপ দেখে।'
            : 'Rongdhonu Kids Wear cuts and sews t-shirts, rompers, sets, dresses and pants for export buyers. Every style keeps the full size run, from 0–3 months to 12 years, in one digital tech pack so merchandising and the sewing line read the same measurements.' }}
    </p>
    <div class="mt-10 grid gap-4 sm:grid-cols-3">
        <div class="rounded-3xl bg-cream p-5"><p class="display text-3xl">{{ kidswear('lines') }}</p><p class="text-sm">sewing lines</p></div>
        <div class="rounded-3xl bg-cream p-5"><p class="display text-3xl">{{ locale_number(kidswear('workers')) }}</p><p class="text-sm">people</p></div>
        <div class="rounded-3xl bg-cream p-5"><p class="display text-3xl">BSCI</p><p class="text-sm">social audit</p></div>
    </div>
</article>
@endsection
