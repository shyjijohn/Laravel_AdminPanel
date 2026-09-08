<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', Rule::in(['first_name', 'last_name', 'company', 'email', 'phone'])],
            'direction' => ['nullable', Rule::in(['asc', 'desc'])],
        ]);

        $search = trim($validated['search'] ?? '');
        $sort = $validated['sort'] ?? 'last_name';
        $direction = $validated['direction'] ?? 'asc';

        $employees = Employee::query()
            ->with('company')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhereHas('company', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when(
                $sort === 'company',
                fn ($query) => $query->orderBy(
                    Company::select('name')->whereColumn('companies.id', 'employees.company_id'),
                    $direction
                ),
                fn ($query) => $query->orderBy($sort, $direction)
            )
            ->when($sort === 'last_name', fn ($query) => $query->orderBy('first_name', $direction))
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view('employees.index', compact('employees', 'search', 'sort', 'direction'));
    }

    public function create(Request $request)
    {
        $companies = Company::orderBy('name')->get();

        $selectedCompanyId = $request->query('company_id');

        return view('employees.create', compact('companies', 'selectedCompanyId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'email' => 'nullable|email|max:255|unique:employees,email',
            'phone' => 'nullable|string|max:20|unique:employees,phone',
        ]);

        Employee::create($validated);

        return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        $employee->load('company');

        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $companies = Company::orderBy('name')->get();

        return view('employees.edit', compact('employee', 'companies'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'email' => 'nullable|email|max:255|unique:employees,email,'.$employee->id,
            'phone' => 'nullable|string|max:20|unique:employees,phone,'.$employee->id,
        ]);

        $employee->update($validated);

        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }
}
