<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date') ? Carbon::parse($request->get('date')) : Carbon::today();

        $employees = Employee::where('status', 'active')->orderBy('name')->get();

        $marked = Attendance::whereDate('date', $date)
            ->get()
            ->keyBy('employee_id');

        return view('attendance.index', [
            'employees' => $employees,
            'marked' => $marked,
            'date' => $date,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'status' => ['required', 'array'],
            'status.*' => ['required', 'in:present,half_day,absent,leave'],
        ]);

        foreach ($validated['status'] as $employeeId => $status) {
            Attendance::updateOrCreate(
                ['employee_id' => $employeeId, 'date' => $validated['date']],
                ['status' => $status]
            );
        }

        return back()->with('status', 'Attendance save ho gayi.');
    }
}
