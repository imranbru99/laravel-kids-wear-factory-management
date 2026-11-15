@extends('admin-panel::layouts.app')
@section('content')
<div class="space-y-4" style="max-width:760px">
    <h1 class="page-title">{{ $record ? 'Edit' : 'New' }} {{ \Illuminate\Support\Str::singular($definition['label']) }}</h1>
    @if ($errors->any())
        <x-admin-panel::card><p>{{ $errors->first() }}</p></x-admin-panel::card>
    @endif
    <form method="post" action="{{ $record ? route('admin.resources.update', [$definition['key'], $record->getKey()]) : route('admin.resources.store', $definition['key']) }}">
        @csrf
        @if ($record) @method('put') @endif
        <x-admin-panel::card>
            <div class="space-y-4">
                @foreach ($definition['fields'] as $field)
                    <label class="block text-sm">
                        <span>{{ $field['label'] }}</span>
                        @if ($field['type'] === 'textarea')
                            <textarea name="{{ $field['name'] }}" class="admin-input w-full" rows="3">{{ old($field['name'], $record->{$field['name']} ?? '') }}</textarea>
                        @elseif ($field['type'] === 'select')
                            <select name="{{ $field['name'] }}" class="admin-input w-full">
                                <option value="">—</option>
                                @foreach ($options[$field['name']] ?? [] as $id => $label)
                                    <option value="{{ $id }}" @selected(old($field['name'], $record->{$field['name']} ?? '') == $id)>{{ $label }}</option>
                                @endforeach
                            </select>
                        @elseif ($field['type'] === 'checkbox')
                            <input type="checkbox" name="{{ $field['name'] }}" value="1" @checked(old($field['name'], $record->{$field['name']} ?? false))>
                        @else
                            <input type="{{ $field['type'] === 'number' ? 'number' : ($field['type'] === 'date' ? 'date' : 'text') }}" step="any" name="{{ $field['name'] }}" value="{{ old($field['name'], isset($record) && $record->{$field['name']} instanceof \DateTimeInterface ? $record->{$field['name']}->format('Y-m-d') : ($record->{$field['name']} ?? '')) }}" class="admin-input w-full">
                        @endif
                    </label>
                @endforeach
                <button class="admin-btn" style="background:var(--admin-primary);color:white">Save</button>
            </div>
        </x-admin-panel::card>
    </form>
</div>
@endsection
