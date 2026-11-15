@extends('admin-panel::layouts.app')
@section('content')
<div class="space-y-4">
    <h1 class="page-title">Attendance · {{ $today }}</h1>
    @if (session('status'))<x-admin-panel::card>{{ session('status') }}</x-admin-panel::card>@endif
    @if ($errors->any())<x-admin-panel::card>{{ $errors->first() }}</x-admin-panel::card>@endif
    @can('hr.manage')
    <x-admin-panel::card title="Mobile check-in">
        <form method="post" action="{{ route('admin.attendance.check-in') }}" class="grid gap-2 md:grid-cols-4">
            @csrf
            <select name="employee_id" class="admin-input" required>@foreach ($employees as $employee)<option value="{{ $employee->id }}">{{ $employee->employee_code }} {{ $employee->name }}</option>@endforeach</select>
            <select name="method" class="admin-input"><option value="gps">GPS geofence</option><option value="qr">QR</option></select>
            <select name="punch_type" class="admin-input"><option value="check_in">Check in</option><option value="check_out">Check out</option></select>
            <button class="admin-btn">Punch</button>
            <input name="lat" class="admin-input" placeholder="Latitude" value="{{ config('kidswear.factory.lat') }}">
            <input name="lng" class="admin-input" placeholder="Longitude" value="{{ config('kidswear.factory.lng') }}">
        </form>
        <form method="post" action="{{ route('admin.attendance.rollup') }}" class="mt-3">@csrf<input type="hidden" name="date" value="{{ $date }}"><button class="admin-btn">Roll up this date</button></form>
    </x-admin-panel::card>
    @endcan
    <x-admin-panel::card>
        <table class="w-full text-sm">
            <thead><tr><th class="text-left">Worker</th><th class="text-left">Status</th><th>Late</th><th>OT</th><th>Worked</th></tr></thead>
            <tbody>
            @foreach ($days as $day)
                <tr class="border-t" style="border-color:var(--admin-border)">
                    <td class="py-2">{{ $day->employee?->employee_code }} {{ $day->employee?->name }}</td>
                    <td>{{ $day->status }}</td>
                    <td>{{ $day->late_minutes }}</td>
                    <td>{{ $day->overtime_minutes }}</td>
                    <td>{{ $day->worked_minutes }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </x-admin-panel::card>
</div>
@endsection
