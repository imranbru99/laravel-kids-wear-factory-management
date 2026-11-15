@extends('admin-panel::layouts.app')
@section('content')
<link rel="stylesheet" href="{{ asset('css/record.css') }}?v={{ filemtime(public_path('css/record.css')) }}">
@php
    $presented = collect($definition['fields'])->map(fn ($field) => $field + \App\Modules\KidsWear\Admin\ResourceCatalog::present($record, $field));
    $scheduleConflict = filled($record->getAttribute('planned_end')) && filled($record->getAttribute('planned_start'))
        && \Illuminate\Support\Carbon::parse($record->planned_end)->lt(\Illuminate\Support\Carbon::parse($record->planned_start));
@endphp
<div class="record-dossier">
    <header class="record-hero">
        <div>
            <p class="record-kicker">{{ $definition['label'] }}</p>
            <h1>{{ data_get($record, $definition['title']) }}</h1>
            <div class="record-pills">
                @foreach ($presented as $field)
                    @if ($field['tone'])
                        <span class="pill {{ $field['tone'] }}">{{ $field['text'] }}</span>
                    @endif
                @endforeach
                @if ($scheduleConflict)
                    <span class="pill bad">End date is before the start</span>
                @endif
            </div>
        </div>
        <div class="page-actions">
            <a class="admin-btn admin-btn-secondary" href="{{ route('admin.resources.index', $definition['key']) }}">Back</a>
            @can($definition['module'].'.manage')
                <a class="admin-btn admin-btn-primary" href="{{ route('admin.resources.edit', [$definition['key'], $record->getKey()]) }}">Edit</a>
                <form method="post" action="{{ route('admin.resources.destroy', [$definition['key'], $record->getKey()]) }}">@csrf @method('delete')<button class="admin-btn admin-btn-danger">Delete</button></form>
            @endcan
        </div>
    </header>

    @if (session('status'))<x-admin-panel::card>{{ session('status') }}</x-admin-panel::card>@endif
    @if ($errors->any())<x-admin-panel::card>{{ $errors->first() }}</x-admin-panel::card>@endif

    <section class="record-grid">
        @foreach ($presented as $field)
            <article class="record-cell {{ in_array($field['type'] ?? '', ['textarea'], true) ? 'wide' : '' }}">
                <span>{{ $field['label'] }}</span>
                <strong>{{ $field['text'] }}</strong>
            </article>
        @endforeach
    </section>

    @includeIf('admin.partials.'.$definition['key'], ['record' => $record])
</div>
@endsection
