@extends('site.layout')
@php $locale = app()->getLocale(); $title = __('kidswear.nav.contact').' · '.kidswear('name'); @endphp
@section('content')
<section class="mx-auto grid max-w-5xl gap-10 px-5 py-14 md:grid-cols-2">
    <div>
        <h1 class="display text-5xl">{{ __('kidswear.nav.contact') }}</h1>
        <p class="mt-4 text-ink/75">{{ $locale === 'bn' ? kidswear('address_bn') : kidswear('address') }}</p>
        <p class="mt-2">{{ kidswear('email') }} · {{ kidswear('phone') }}</p>
    </div>
    <form method="post" action="{{ route('inquiry.store', $locale) }}" class="space-y-3 rounded-[1.6rem] bg-white p-6 ring-1 ring-sand">
        @csrf
        <input type="hidden" name="type" value="contact">
        <input name="name" value="{{ old('name') }}" required placeholder="Name" class="w-full rounded-xl border border-sand px-3 py-2">
        <input name="email" type="email" value="{{ old('email') }}" required placeholder="Email" class="w-full rounded-xl border border-sand px-3 py-2">
        <input name="phone" value="{{ old('phone') }}" placeholder="Phone" class="w-full rounded-xl border border-sand px-3 py-2">
        <textarea name="message" required placeholder="Message" class="w-full rounded-xl border border-sand px-3 py-2">{{ old('message') }}</textarea>
        @if ($errors->any())
            <p class="text-sm text-coral">{{ $errors->first() }}</p>
        @endif
        <button class="rounded-full bg-coral px-4 py-2 text-sm font-semibold text-white">Send</button>
    </form>
</section>
@endsection
