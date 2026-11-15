<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\KidsWear\Hr\Models\AttendanceDay;
use App\Modules\KidsWear\Hr\Models\Employee;
use App\Modules\KidsWear\Hr\Services\AttendanceService;
use App\Modules\KidsWear\Support\FactoryCalendar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(): View
    {
        abort_unless(module_enabled('hr') && auth()->user()?->can('hr.view'), 403);
        $date = request('date', FactoryCalendar::today()->toDateString());

        return view('admin.attendance.index', [
            'date' => $date,
            'days' => AttendanceDay::query()->with('employee.department')->whereDate('work_date', $date)->orderBy('status')->get(),
            'employees' => Employee::query()->where('status', 'active')->orderBy('name')->get(),
            'today' => FactoryCalendar::label(FactoryCalendar::parse($date)),
        ]);
    }

    public function checkIn(Request $request, AttendanceService $attendance): RedirectResponse
    {
        abort_unless(auth()->user()?->can('hr.manage'), 403);
        $data = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'method' => ['required', 'in:gps,qr'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'punch_type' => ['required', 'in:check_in,check_out'],
        ]);

        $employee = Employee::query()->findOrFail($data['employee_id']);
        $attendance->checkIn(
            $employee,
            $data['method'],
            isset($data['lat']) ? (float) $data['lat'] : (float) config('kidswear.factory.lat'),
            isset($data['lng']) ? (float) $data['lng'] : (float) config('kidswear.factory.lng'),
            $data['punch_type'],
        );

        return back()->with('status', $employee->name.' punched '.$data['punch_type'].' via '.$data['method'].'.');
    }

    public function rollup(AttendanceService $attendance): RedirectResponse
    {
        abort_unless(auth()->user()?->can('hr.manage'), 403);
        $date = request('date', FactoryCalendar::today()->toDateString());
        $count = $attendance->rollupDate($date);

        return back()->with('status', 'Rolled up attendance for '.$count.' workers on '.$date.'.');
    }
}
