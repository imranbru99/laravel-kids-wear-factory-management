@php $record->loadMissing('defects.defectType'); $types = \App\Modules\KidsWear\Quality\Models\DefectType::query()->orderBy('name')->get(); @endphp
<x-admin-panel::card title="Defect checklist">
    @foreach ($record->defects as $defect)<p class="text-sm">{{ $defect->defectType->name }} · {{ $defect->quantity }}</p>@endforeach
    @if ($record->ai_notes)<p class="mt-2 text-sm">{{ $record->ai_notes }}</p>@endif
    @can('quality.manage')
        <form method="post" action="{{ route('admin.inspections.defects', $record) }}" class="mt-3 grid gap-2 md:grid-cols-2">
            @csrf
            @foreach ($types as $type)
                <label class="text-sm">{{ $type->name }}<input type="number" min="0" name="defects[{{ $type->id }}]" class="admin-input w-full" value="0"></label>
            @endforeach
            <button class="admin-btn">Save defects & ask AI Hub</button>
        </form>
    @endcan
</x-admin-panel::card>
