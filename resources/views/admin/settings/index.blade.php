@extends('admin-panel::layouts.app')
@section('content')
<div class="space-y-4" style="max-width:840px">
    <h1 class="page-title">Settings</h1>
    @if (session('status'))<x-admin-panel::card>{{ session('status') }}</x-admin-panel::card>@endif
    <form method="post" action="{{ route('admin.settings.save') }}">
        @csrf
        <x-admin-panel::card title="Company">
            <div class="grid gap-2 md:grid-cols-2">
                @foreach (['name','name_bn','tagline','tagline_bn','address','address_bn','phone','email'] as $field)
                    <label class="text-sm">{{ str($field)->headline() }}<input name="{{ $field }}" value="{{ old($field, $company[$field] ?? '') }}" class="admin-input w-full"></label>
                @endforeach
            </div>
        </x-admin-panel::card>
        <x-admin-panel::card title="Modules" class="mt-4">
            @foreach ($modules as $key => $module)
                <label class="mr-4 text-sm"><input type="checkbox" name="modules[{{ $key }}]" value="1" @checked($flags === [] ? $module['enabled'] : ($flags[$key] ?? false))> {{ $module['label'] }}</label>
            @endforeach
            @can('settings.manage')<button class="admin-btn mt-4">Save</button>@endcan
        </x-admin-panel::card>
    </form>
    <x-admin-panel::card title="Backup & speed">
        <p class="text-sm">Last backup: {{ $backup['at'] ?? 'never' }} {{ $backup['path'] ?? ($backup['note'] ?? '') }}</p>
        @can('settings.manage')
            <form method="post" action="{{ route('admin.settings.backup') }}" class="mt-2">@csrf<button class="admin-btn">Queue backup</button></form>
        @endcan
        <div class="mt-4 text-sm">
            @forelse ($suggestions as $suggestion)
                <p><strong>{{ $suggestion->severity }}</strong> {{ $suggestion->title }}</p>
            @empty
                <p>Drutogoti has no suggestions yet. Browse the factory and the profiler will queue them when it is enabled.</p>
            @endforelse
            <p class="mt-2"><a href="{{ url('/ai-hub') }}">AI Hub studio</a> · <a href="{{ url('/ai-catalog') }}">AI Catalog studio</a> · <a href="{{ url('/ai-ocr') }}">AI OCR studio</a></p>
        </div>
    </x-admin-panel::card>
</div>
@endsection
