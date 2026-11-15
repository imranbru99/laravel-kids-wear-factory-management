@extends('admin-panel::layouts.app')
@section('content')
<div class="space-y-4">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $definition['label'] }}</h1>
            <p class="page-subtitle">{{ $definition['group'] }}</p>
        </div>
        @can($definition['module'].'.manage')
            <a href="{{ route('admin.resources.create', $definition['key']) }}" class="admin-btn" style="background:var(--admin-primary);color:white">New</a>
        @endcan
    </div>
    @if (session('status'))<x-admin-panel::card>{{ session('status') }}</x-admin-panel::card>@endif
    <form><input name="q" value="{{ request('q') }}" placeholder="Search" class="admin-input" style="max-width:280px"></form>
    <x-admin-panel::card>
        <table class="w-full text-sm">
            <thead>
                <tr>
                    @foreach ($definition['columns'] as $column)
                        <th class="py-2 text-left">{{ str($column)->headline() }}</th>
                    @endforeach
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse ($records as $record)
                <tr class="border-t" style="border-color:var(--admin-border)">
                    @foreach ($definition['columns'] as $column)
                        <td class="py-2">{{ data_get($record, $column) }}</td>
                    @endforeach
                    <td class="py-2 text-right"><a href="{{ route('admin.resources.show', [$definition['key'], $record->getKey()]) }}">Open</a></td>
                </tr>
            @empty
                <tr><td colspan="{{ count($definition['columns']) + 1 }}" class="py-6">Nothing here yet.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $records->links() }}</div>
    </x-admin-panel::card>
</div>
@endsection
