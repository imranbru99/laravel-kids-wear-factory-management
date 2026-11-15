@php $record->loadMissing('items.material', 'receipts'); @endphp
<x-admin-panel::card title="Lines & GRN">
    @foreach ($record->items as $item)
        <p class="text-sm">{{ $item->description }} · {{ $item->quantity }} {{ $item->unit }} · received {{ $item->received_qty }} · {{ $item->unit_price }}</p>
    @endforeach
    @can('procurement.manage')
        <form method="post" action="{{ route('admin.purchase.items', $record) }}" class="mt-3 grid gap-2 md:grid-cols-3">
            @csrf
            <select name="material_id" class="admin-input" required>@foreach (\App\Modules\KidsWear\Inventory\Models\Material::query()->orderBy('name')->get() as $material)<option value="{{ $material->id }}">{{ $material->name }}</option>@endforeach</select>
            <input name="quantity" type="number" step="0.001" class="admin-input" required>
            <input name="unit_price" type="number" step="0.0001" class="admin-input" required>
            <button class="admin-btn">Add line</button>
        </form>
        <form method="post" action="{{ route('admin.purchase.receive', $record) }}" class="mt-3 flex gap-2">
            @csrf
            <select name="location_id" class="admin-input" required>@foreach (\App\Modules\KidsWear\Inventory\Models\Location::query()->get() as $location)<option value="{{ $location->id }}">{{ $location->name }}</option>@endforeach</select>
            <button class="admin-btn">Receive all (GRN)</button>
        </form>
    @endcan
</x-admin-panel::card>
