<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::latest()->paginate(20);

        return view('employees.index', ['employees' => $employees]);
    }

    public function create()
    {
        return view('employees.create', ['nextCode' => $this->nextCode()]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $employee = Employee::create($validated);

        return redirect()->route('employees.index')->with('status', "{$employee->name} add ho gaya.");
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', ['employee' => $employee]);
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $this->validated($request, $employee->id);

        $employee->update($validated);

        return redirect()->route('employees.index')->with('status', 'Employee update ho gaya.');
    }

    public function destroy(Employee $employee)
    {
        $employee->update(['status' => 'inactive']);

        return redirect()->route('employees.index')->with('status', "{$employee->name} inactive kar diya gaya (record delete nahi hota, history ke liye).");
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'employee_code' => ['required', 'string', 'max:50', 'unique:employees,employee_code'.($ignoreId ? ",$ignoreId" : '')],
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'aadhaar_number' => ['nullable', 'string', 'max:20'],
            'pan_number' => ['nullable', 'string', 'max:20'],
            'joining_date' => ['nullable', 'date'],
            'department' => ['nullable', 'string', 'max:100'],
            'designation' => ['nullable', 'string', 'max:100'],
            'per_day_salary' => ['required', 'numeric', 'min:0'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_ifsc' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'in:active,inactive'],
        ]);
    }

    protected function nextCode(): string
    {
        $last = Employee::max('id') ?? 0;

        return 'EMP-'.str_pad((string) ($last + 1), 4, '0', STR_PAD_LEFT);
    }
}
