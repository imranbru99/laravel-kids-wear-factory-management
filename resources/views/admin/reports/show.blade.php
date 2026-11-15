@extends('admin-panel::layouts.app')
@section('content')
<div class="space-y-4">
    <h1 class="page-title">{{ $title }}</h1>
    <p><a href="{{ route('admin.reports.xlsx', $key) }}">Excel</a> · <a href="{{ route('admin.reports.pdf', $key) }}">PDF</a></p>
    <x-admin-panel::card>
        <table class="w-full text-sm">
            <thead><tr>@foreach ($table['headers'] as $header)<th class="py-2 text-left">{{ $header }}</th>@endforeach</tr></thead>
            <tbody>
            @foreach ($table['rows'] as $row)
                <tr class="border-t" style="border-color:var(--admin-border)">@foreach ($row as $cell)<td class="py-1">{{ $cell }}</td>@endforeach</tr>
            @endforeach
            </tbody>
        </table>
    </x-admin-panel::card>
</div>
@endsection
