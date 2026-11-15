@extends('site.layout')
@php $locale = app()->getLocale(); $title = __('kidswear.nav.process').' · '.kidswear('name'); @endphp
@section('content')
<section class="mx-auto max-w-4xl px-5 py-16">
    <h1 class="display text-5xl">{{ $locale === 'bn' ? 'অর্ডার থেকে কার্টন' : 'From order to carton' }}</h1>
    <ol class="mt-10 space-y-6">
        @foreach ([
            ['01', 'Buyer order', 'Size-by-color matrix is captured once. OCR can draft it from a PDF.'],
            ['02', 'BOM & MRP', 'Fabric, trims and packing are calculated from the tech pack, including wastage.'],
            ['03', 'Bundles', 'Each size is split into QR bundles so a lost lot can be found by code.'],
            ['04', 'Cut, sew, finish, pack', 'Daily output and rejects move the order across the floor board.'],
            ['05', 'Inspection', 'In-line and final AQL checks flag shade, seam, measurement and safety issues early.'],
            ['06', 'Documents', 'Packing lists and commercial invoices print in Bengali and English.'],
        ] as [$n, $title, $copy])
            <li class="grid grid-cols-[auto_1fr] gap-5 rounded-3xl bg-white p-5 ring-1 ring-sand">
                <span class="display text-3xl text-coral">{{ $n }}</span>
                <div>
                    <h2 class="display text-2xl">{{ $title }}</h2>
                    <p class="mt-1 text-ink/70">{{ $copy }}</p>
                </div>
            </li>
        @endforeach
    </ol>
</section>
@endsection
