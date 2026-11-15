@php
    $record->loadMissing('lines.style', 'requirements', 'bundles', 'entries');
    $plan = $record->lines->first() ? \App\Modules\KidsWear\Support\CuttingPlan::suggest($record->lines->first()->size_matrix ?? []) : null;
@endphp
<div class="space-y-4">
    <x-admin-panel::card title="Material requirement">
        <table class="record-table">
            <thead><tr><th>Material</th><th>Need</th><th>On hand</th><th>Short</th><th>Issued</th></tr></thead>
            <tbody>
            @forelse ($record->requirements as $row)
                <tr>
                    <td>{{ $row->name }}</td>
                    <td>{{ $row->required_qty }} {{ $row->unit }}</td>
                    <td>{{ $row->available_qty }}</td>
                    <td>{{ $row->shortage_qty }}</td>
                    <td>{{ $row->issued_qty }}</td>
                </tr>
            @empty
                <tr><td colspan="5">No requirement yet. Recalculate the BOM from the style.</td></tr>
            @endforelse
            </tbody>
        </table>
        @if ($plan)
            <p class="record-note">Cutting ratio {{ \App\Modules\KidsWear\Support\SizeMatrix::label($plan['ratio']) }} · {{ $plan['lays'] }} lays</p>
        @endif
        <div class="record-actions">
            @can('production.manage')
                <form method="post" action="{{ route('admin.production.recalculate', $record) }}">@csrf<button class="admin-btn admin-btn-secondary">Recalculate BOM</button></form>
            @endcan
            @can('inventory.manage')
                <form method="post" action="{{ route('admin.production.issue', $record) }}">@csrf<button class="admin-btn admin-btn-secondary">Issue material to floor</button></form>
            @endcan
            @can('finance.manage')
                <form method="post" action="{{ route('admin.production.packing-list', $record) }}">@csrf<button class="admin-btn admin-btn-secondary">Build packing list</button></form>
            @endcan
        </div>
    </x-admin-panel::card>
    <x-admin-panel::card title="Bundles">
        <table class="record-table">
            <thead><tr><th>Ticket</th><th>Size</th><th>Qty</th><th>Stage</th><th></th></tr></thead>
            <tbody>
            @forelse ($record->bundles as $bundle)
                <tr>
                    <td>{{ $bundle->code }}</td>
                    <td>{{ $bundle->size_code }}</td>
                    <td>{{ $bundle->quantity }}</td>
                    <td>{{ str($bundle->current_stage)->headline() }}</td>
                    <td><a href="{{ route('admin.bundles.ticket', $bundle->code) }}">Ticket</a> · <a href="{{ route('track', $bundle->code) }}">Track</a></td>
                </tr>
            @empty
                <tr><td colspan="5">No bundles cut yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </x-admin-panel::card>
    @can('production.manage')
    <x-admin-panel::card title="Record stage output">
        <form method="post" action="{{ route('admin.production.output', $record) }}" class="grid gap-2 md:grid-cols-3">
            @csrf
            <select name="stage" class="admin-input">@foreach (\App\Modules\KidsWear\Support\Status::productionStages() as $stage)<option>{{ $stage }}</option>@endforeach</select>
            <select name="bundle_id" class="admin-input"><option value="">Whole order</option>@foreach ($record->bundles as $bundle)<option value="{{ $bundle->id }}">{{ $bundle->code }}</option>@endforeach</select>
            <input type="date" name="entry_date" value="{{ now()->toDateString() }}" class="admin-input" required>
            <input type="number" name="quantity_out" placeholder="Good pcs" class="admin-input" required>
            <input type="number" name="quantity_reject" placeholder="Rejects" class="admin-input">
            <button class="admin-btn admin-btn-primary">Save output</button>
        </form>
    </x-admin-panel::card>
    @endcan
</div>
