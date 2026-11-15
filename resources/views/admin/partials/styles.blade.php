@php
    $record->loadMissing('colorways', 'bomItems.material', 'sizeCharts.rows', 'techPacks', 'costings');
@endphp
<div class="grid gap-4 lg:grid-cols-2">
    <x-admin-panel::card title="Colorways">
        <ul class="text-sm">@foreach ($record->colorways as $color)<li>{{ $color->code }} · {{ $color->name }}</li>@endforeach</ul>
        @can('catalog.manage')
        <form method="post" action="{{ route('admin.styles.colorways', $record) }}" class="mt-3 grid grid-cols-3 gap-2">
            @csrf
            <input name="name" placeholder="Name" class="admin-input" required>
            <input name="code" placeholder="Code" class="admin-input" required>
            <input name="hex" placeholder="#hex" class="admin-input">
            <button class="admin-btn">Add</button>
        </form>
        @endcan
    </x-admin-panel::card>
    <x-admin-panel::card title="Bill of materials">
        <ul class="text-sm">@foreach ($record->bomItems as $item)<li>{{ $item->name }} · {{ $item->consumption }} {{ $item->unit }} + {{ $item->wastage_percent }}%</li>@endforeach</ul>
        @can('catalog.manage')
        <form method="post" action="{{ route('admin.styles.bom', $record) }}" class="mt-3 grid gap-2">
            @csrf
            <select name="material_id" class="admin-input"><option value="">Free text</option>@foreach (\App\Modules\KidsWear\Inventory\Models\Material::query()->orderBy('name')->get() as $material)<option value="{{ $material->id }}">{{ $material->sku }}</option>@endforeach</select>
            <input name="component" placeholder="fabric / trim / packing" class="admin-input" required>
            <input name="name" placeholder="Name" class="admin-input" required>
            <input name="unit" placeholder="Unit" class="admin-input" required>
            <input name="consumption" type="number" step="0.0001" placeholder="Consumption per piece" class="admin-input" required>
            <input name="wastage_percent" type="number" step="0.01" placeholder="Wastage %" class="admin-input">
            <button class="admin-btn">Add BOM line</button>
        </form>
        @endcan
    </x-admin-panel::card>
    <x-admin-panel::card title="Size chart">
        @foreach ($record->sizeCharts as $chart)
            @foreach ($chart->rows as $row)
                <p class="text-sm">{{ $row->size_code }} · chest {{ $row->measurements['chest'] ?? '—' }} · length {{ $row->measurements['length'] ?? '—' }}</p>
            @endforeach
        @endforeach
        @can('catalog.manage')
        <form method="post" action="{{ route('admin.styles.measurements', $record) }}" class="mt-3 grid grid-cols-4 gap-2">
            @csrf
            <select name="size_code" class="admin-input">@foreach (config('kidswear.sizes') as $size)<option>{{ $size }}</option>@endforeach</select>
            <input name="chest" placeholder="Chest" class="admin-input">
            <input name="length" placeholder="Length" class="admin-input">
            <input name="waist" placeholder="Waist" class="admin-input">
            <button class="admin-btn">Save size</button>
        </form>
        @endcan
    </x-admin-panel::card>
    <x-admin-panel::card title="Tech pack & costing">
        @foreach ($record->techPacks as $pack)<p class="text-sm">Rev {{ $pack->version }} · {{ $pack->construction }}</p>@endforeach
        @foreach ($record->costings as $cost)<p class="text-sm">FOB {{ $cost->fob_price }} {{ $cost->currency }}</p>@endforeach
        @can('catalog.manage')
        <form method="post" action="{{ route('admin.styles.tech-packs', $record) }}" class="mt-3 grid gap-2">
            @csrf
            <input name="version" placeholder="Version" class="admin-input" required>
            <textarea name="construction" placeholder="Construction" class="admin-input"></textarea>
            <textarea name="stitching" placeholder="Stitching" class="admin-input"></textarea>
            <button class="admin-btn">Save tech pack</button>
        </form>
        @endcan
        @can('finance.manage')
        <form method="post" action="{{ route('admin.styles.costing', $record) }}" class="mt-3 grid grid-cols-2 gap-2">
            @csrf
            @foreach (['fabric_cost','trim_cost','cm_cost','washing_cost','print_cost','packing_cost','overhead','margin_percent'] as $field)
                <input name="{{ $field }}" type="number" step="0.0001" placeholder="{{ str($field)->headline() }}" class="admin-input">
            @endforeach
            <button class="admin-btn">Calculate FOB</button>
        </form>
        @endcan
    </x-admin-panel::card>
</div>
