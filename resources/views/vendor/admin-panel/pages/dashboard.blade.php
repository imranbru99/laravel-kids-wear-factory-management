@extends('admin-panel::layouts.app')
@section('content')
<div class="space-y-6">
    <div class="page-header">
        <div>
            <h1 class="page-title">Factory floor</h1>
            <p class="page-subtitle">{{ kidswear('name') }} · {{ \App\Modules\KidsWear\Support\FactoryCalendar::label(\App\Modules\KidsWear\Support\FactoryCalendar::today()) }}</p>
        </div>
        <div class="page-actions">
            <form method="post" action="{{ route('logout') }}">@csrf<button class="admin-btn">Sign out</button></form>
        </div>
    </div>
    @if (session('status'))
        <x-admin-panel::card><p>{{ session('status') }}</p></x-admin-panel::card>
    @endif
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;">
        @foreach ([
            ['Today output', $kpis['output'].' pcs'],
            ['Efficiency', $kpis['efficiency'].'%'],
            ['Open orders', $kpis['pending_orders']],
            ['Present / late', $kpis['present'].' / '.$kpis['late']],
            ['Material alerts', $kpis['material_alerts']],
            ['7-day rejection', $kpis['rejection'].'%'],
            ['On-time delivery', $kpis['on_time'].'%'],
            ['Delayed orders', $kpis['delayed']],
        ] as [$label, $value])
            <x-admin-panel::card>
                <p style="font-size:.8rem;color:var(--admin-text-muted)">{{ $label }}</p>
                <p style="font-size:1.6rem;font-weight:700">{{ $value }}</p>
            </x-admin-panel::card>
        @endforeach
    </div>
    <x-admin-panel::card title="Recent factory activity">
        @forelse ($kpis['activity'] as $activity)
            <p class="text-sm" style="margin-bottom:.5rem">{{ $activity->description }} · {{ $activity->created_at?->diffForHumans() }}</p>
        @empty
            <p class="text-sm">No audit rows yet.</p>
        @endforelse
    </x-admin-panel::card>
</div>
@endsection
