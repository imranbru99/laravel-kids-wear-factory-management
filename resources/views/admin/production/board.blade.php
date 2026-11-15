@extends('admin-panel::layouts.app')
@section('content')
<div class="space-y-4">
    <h1 class="page-title">Floor board</h1>
    <p class="page-subtitle">Today's good output: {{ $todayOut }} pcs</p>
    <div class="grid gap-4 md:grid-cols-4">
        @foreach ($stages as $stage)
            <x-admin-panel::card :title="ucfirst($stage)">
                @foreach ($orders->where('status', $stage) as $order)
                    <a class="block text-sm" href="{{ route('admin.resources.show', ['production-orders', $order->id]) }}">{{ $order->number }} · {{ $order->buyerOrder?->buyer?->code }}</a>
                @endforeach
            </x-admin-panel::card>
        @endforeach
    </div>
    <x-admin-panel::card title="Also on the board">
        @foreach ($orders->whereNotIn('status', $stages) as $order)
            <a class="block text-sm" href="{{ route('admin.resources.show', ['production-orders', $order->id]) }}">{{ $order->number }} · {{ $order->status }} · due {{ $order->planned_end }}</a>
        @endforeach
    </x-admin-panel::card>
</div>
@endsection
