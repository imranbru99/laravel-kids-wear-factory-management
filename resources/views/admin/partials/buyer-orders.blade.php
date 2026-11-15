@php $record->loadMissing('lines.style', 'lines.colorway'); @endphp
<x-admin-panel::card title="Size matrix">
    @foreach ($record->lines as $line)
        <p class="text-sm">{{ $line->style->style_code }} · {{ \App\Modules\KidsWear\Support\SizeMatrix::label($line->size_matrix) }} · {{ $line->quantity }} pcs · {{ $line->unit_price }}</p>
    @endforeach
    @can('orders.manage')
        <form method="post" action="{{ route('admin.orders.lines', $record) }}" class="mt-4 space-y-2">
            @csrf
            <select name="style_id" class="admin-input" required>@foreach (\App\Modules\KidsWear\Catalog\Models\Style::query()->orderBy('style_code')->get() as $style)<option value="{{ $style->id }}">{{ $style->style_code }} {{ $style->name }}</option>@endforeach</select>
            <input name="unit_price" type="number" step="0.01" placeholder="Unit price" class="admin-input" required>
            <div class="grid grid-cols-3 gap-2 md:grid-cols-5">
                @foreach (config('kidswear.sizes') as $size)
                    <label class="text-xs">{{ $size }}<input name="sizes[{{ $size }}]" type="number" min="0" class="admin-input w-full" value="0"></label>
                @endforeach
            </div>
            <button class="admin-btn">Add size run</button>
        </form>
        <form method="post" action="{{ route('admin.orders.confirm', $record) }}" class="mt-3">@csrf<button class="admin-btn" style="background:var(--admin-primary);color:white">Confirm and open production</button></form>
    @endcan
    @can('finance.manage')
        <form method="post" action="{{ route('admin.orders.invoice', $record) }}" class="mt-3">@csrf<button class="admin-btn">Issue commercial invoice</button></form>
    @endcan
</x-admin-panel::card>
