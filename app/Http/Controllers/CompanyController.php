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
        $validated = $request->validate([
            'name'    => 'required|string|max:255|unique:companies,name',
            'email'   => 'required|email|max:255|unique:companies,email',
            'website' => 'nullable|url|max:255',
            'logo'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=100,min_height=100',
        ]);

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
        $validated = $request->validate([
            'name'    => 'required|string|max:255|unique:companies,name,' . $company->id,
            'email'   => 'required|email|max:255|unique:companies,email,' . $company->id,
            'website' => 'nullable|url|max:255',
            'logo'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=100,min_height=100',
        ]);

        if ($request->hasFile('logo')) {
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