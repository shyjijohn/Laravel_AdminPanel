<?php

namespace App\Http\Controllers;

use App\Models\Company;  
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    
    public function index()
    {
        $employees = Employee::with('company')->latest()->paginate(10);

        return view('employees.index', compact('employees'));
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
        'last_name'  => 'required|string|max:255',
        'company_id' => 'required|exists:companies,id',
        'email'      => 'nullable|email|max:255',
        'phone'      => 'nullable|string|max:20',
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
            'last_name'  => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'email'      => 'nullable|email|max:255',
            'phone'      => 'nullable|string|max:20',
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
