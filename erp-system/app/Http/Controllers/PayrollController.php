<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\SalarySlip;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);

        $employees = Employee::where('status', 'active')->orderBy('name')->get();

        $slips = SalarySlip::where('month', $month)->where('year', $year)->get()->keyBy('employee_id');

        return view('payroll.index', compact('employees', 'slips', 'month', 'year'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'min:2020'],
        ]);

        $month = $validated['month'];
        $year = $validated['year'];

        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = Carbon::create($year, $month, 1)->endOfMonth();

        $employees = Employee::where('status', 'active')->get();

        foreach ($employees as $employee) {
            $attendances = Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$start, $end])
                ->get();

            $presentDays = $attendances->sum->dayValue();
            $gross = round($presentDays * $employee->per_day_salary, 2);

            $existing = SalarySlip::where('employee_id', $employee->id)
                ->where('month', $month)->where('year', $year)->first();

            SalarySlip::updateOrCreate(
                ['employee_id' => $employee->id, 'month' => $month, 'year' => $year],
                [
                    'present_days' => $presentDays,
                    'per_day_salary' => $employee->per_day_salary,
                    'gross_salary' => $gross,
                    'advance_deduction' => $existing->advance_deduction ?? 0,
                    'other_deduction' => $existing->other_deduction ?? 0,
                    'net_salary' => $gross - ($existing->advance_deduction ?? 0) - ($existing->other_deduction ?? 0),
                    'status' => $existing->status ?? 'generated',
                ]
            );
        }

        return redirect()->route('payroll.index', ['month' => $month, 'year' => $year])
            ->with('status', 'Salary slips generate ho gayi.');
    }

    public function show(SalarySlip $salarySlip)
    {
        $salarySlip->load('employee');

        return view('payroll.show', ['slip' => $salarySlip]);
    }

    public function update(Request $request, SalarySlip $salarySlip)
    {
        $validated = $request->validate([
            'advance_deduction' => ['required', 'numeric', 'min:0'],
            'other_deduction' => ['required', 'numeric', 'min:0'],
        ]);

        $net = $salarySlip->gross_salary - $validated['advance_deduction'] - $validated['other_deduction'];

        $salarySlip->update([
            ...$validated,
            'net_salary' => $net,
        ]);

        return back()->with('status', 'Salary slip update ho gayi.');
    }

    public function markPaid(SalarySlip $salarySlip)
    {
        $salarySlip->update(['status' => 'paid', 'paid_at' => now()]);

        return back()->with('status', 'Salary paid mark ho gayi.');
    }
}
