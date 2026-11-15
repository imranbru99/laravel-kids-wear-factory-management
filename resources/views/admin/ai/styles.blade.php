@extends('admin-panel::layouts.app')
@section('content')
<div class="space-y-4" style="max-width:720px">
    <h1 class="page-title">AI style desk</h1>
    <p class="page-subtitle">Laravel AI Catalog writes the listing when a provider key is configured. Without a key, the desk still opens a merchandising draft and a cutting-ratio note.</p>
    <form method="post" action="{{ route('admin.ai.styles.store') }}">
        @csrf
        <x-admin-panel::card>
            <input name="keyword" required placeholder="e.g. striped cotton romper 0-12 months" class="admin-input w-full">
            <button class="admin-btn mt-3">Generate draft</button>
        </x-admin-panel::card>
    </form>
    @foreach ($styles as $style)
        <a class="block text-sm" href="{{ route('admin.resources.show', ['styles', $style->id]) }}">{{ $style->style_code }} {{ $style->name }}</a>
    @endforeach
</div>
@endsection
