<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCompanyRequest;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::paginate(10);
        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(StoreCompanyRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Saves directly to public/logos/
            $file->move(public_path('logos'), $filename);
            
            $validated['logo'] = 'logos/' . $filename;
        }

        Company::create($validated);
        return redirect()->route('companies.index')->with('success', 'Company created!');
    }

    public function show(Company $company)
    {
        $employees = $company->employees()->paginate(10);
        return view('companies.show', compact('company', 'employees'));
    }

    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    public function update(StoreCompanyRequest $request, Company $company)
    {
        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            // Remove old file if present
            if ($company->logo && file_exists(public_path($company->logo))) {
                @unlink(public_path($company->logo));
            }

            $file = $request->file('logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            $file->move(public_path('logos'), $filename);
            $validated['logo'] = 'logos/' . $filename;
        }

        $company->update($validated);

        return redirect()->route('companies.index')
            ->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        if ($company->logo && file_exists(public_path($company->logo))) {
            @unlink(public_path($company->logo));
        }

        $company->delete();

        return redirect()->route('companies.index')
            ->with('success', 'Company deleted successfully.');
    }
}