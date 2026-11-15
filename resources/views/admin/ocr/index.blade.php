@extends('admin-panel::layouts.app')
@section('content')
<div class="space-y-4" style="max-width:720px">
    <h1 class="page-title">OCR order intake</h1>
    <p class="page-subtitle">Upload a text or PDF buyer order. Laravel AI OCR reads it locally. A matching style code and size lines become a draft order.</p>
    @if (session('status'))<x-admin-panel::card>{{ session('status') }}</x-admin-panel::card>@endif
    <form method="post" action="{{ route('admin.ocr.store') }}" enctype="multipart/form-data">
        @csrf
        <x-admin-panel::card>
            <input type="file" name="document" required class="admin-input">
            <button class="admin-btn mt-3">Extract</button>
        </x-admin-panel::card>
    </form>
    @foreach ($intakes as $intake)
        <x-admin-panel::card>
            <p class="text-sm font-semibold">{{ $intake->original_name }} · {{ $intake->status }}</p>
            <pre class="mt-2 whitespace-pre-wrap text-xs">{{ \Illuminate\Support\Str::limit($intake->extracted_text, 400) }}</pre>
        </x-admin-panel::card>
    @endforeach
</div>
@endsection
