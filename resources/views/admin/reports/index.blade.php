@extends('admin-panel::layouts.app')
@section('content')
<div class="space-y-3">
    <h1 class="page-title">Reports</h1>
    @foreach ($names as $key => $label)
        <x-admin-panel::card>
            <a href="{{ route('admin.reports.show', $key) }}">{{ $label }}</a>
            <span class="text-sm"> · <a href="{{ route('admin.reports.xlsx', $key) }}">Excel</a> · <a href="{{ route('admin.reports.pdf', $key) }}">PDF</a></span>
        </x-admin-panel::card>
    @endforeach
</div>
@endsection
