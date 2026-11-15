@extends('site.layout')
@php $title = $bundle->code; @endphp
@section('content')
<section class="mx-auto max-w-xl px-5 py-16">
    <p class="text-xs uppercase tracking-wider text-leaf">Bundle</p>
    <h1 class="display text-4xl">{{ $bundle->code }}</h1>
    <dl class="mt-6 space-y-2 text-sm">
        <div class="flex justify-between border-b border-sand py-2"><dt>Style</dt><dd>{{ $bundle->style?->style_code }}</dd></div>
        <div class="flex justify-between border-b border-sand py-2"><dt>Size</dt><dd>{{ $bundle->size_code }} · {{ $bundle->quantity }} pcs</dd></div>
        <div class="flex justify-between border-b border-sand py-2"><dt>Stage</dt><dd>{{ $bundle->current_stage }}</dd></div>
        <div class="flex justify-between border-b border-sand py-2"><dt>Status</dt><dd>{{ $bundle->status }}</dd></div>
        <div class="flex justify-between py-2"><dt>Order</dt><dd>{{ $bundle->productionOrder?->number }}</dd></div>
    </dl>
</section>
@endsection
