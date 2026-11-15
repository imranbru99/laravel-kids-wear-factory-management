@extends('site.layout')
@php $title = __('kidswear.nav.certifications').' · '.kidswear('name'); @endphp
@section('content')
<section class="mx-auto max-w-4xl px-5 py-16">
    <h1 class="display text-5xl">{{ __('kidswear.nav.certifications') }}</h1>
    <div class="mt-8 space-y-4">
        @foreach ($certs as $cert)
            <article class="rounded-3xl bg-cream p-6">
                <p class="text-xs font-semibold uppercase tracking-wider text-leaf">{{ $cert->code }} · {{ $cert->issuer }}</p>
                <h2 class="display mt-2 text-3xl">{{ $cert->name }}</h2>
                <p class="mt-2 text-ink/75">{{ app()->getLocale() === 'bn' && $cert->summary_bn ? $cert->summary_bn : $cert->summary }}</p>
                @if ($cert->valid_until)
                    <p class="mt-3 text-sm text-ink/50">Valid until {{ $cert->valid_until->toFormattedDateString() }}</p>
                @endif
            </article>
        @endforeach
    </div>
</section>
@endsection
