<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', Rule::in(['name', 'email', 'website'])],
            'direction' => ['nullable', Rule::in(['asc', 'desc'])],
        ]);

        $search = trim($validated['search'] ?? '');
        $sort = $validated['sort'] ?? 'name';
        $direction = $validated['direction'] ?? 'asc';

        $companies = Company::query()
            ->withCount('employees')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('website', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction)
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view('companies.index', compact('companies', 'search', 'sort', 'direction'));
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(StoreCompanyRequest $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:companies,name',
            'email' => 'required|email|max:255|unique:companies,email',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=100,min_height=100',
        ]);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time().'_'.$file->getClientOriginalName();

            // Saves directly to public/logos/
            $file->move(public_path('logos'), $filename);

            $validated['logo'] = 'logos/'.$filename;
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
            'name' => 'required|string|max:255|unique:companies,name,'.$company->id,
            'email' => 'required|email|max:255|unique:companies,email,'.$company->id,
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=100,min_height=100',
        ]);

        if ($request->hasFile('logo')) {
            if ($company->logo && file_exists(public_path($company->logo))) {
                @unlink(public_path($company->logo));
            }

            $file = $request->file('logo');
            $filename = time().'_'.$file->getClientOriginalName();

            $file->move(public_path('logos'), $filename);
            $validated['logo'] = 'logos/'.$filename;
        }

        $company->update($validated);

        return redirect()->route('companies.index')
            ->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        if ($company->employees()->exists()) {
            return redirect()->route('companies.index')
                ->with('error', 'This company cannot be deleted while employees are assigned to it. Reassign or delete those employees first.');
        }

        if ($company->logo && file_exists(public_path($company->logo))) {
            @unlink(public_path($company->logo));
        }

        $company->delete();

        return redirect()->route('companies.index')
            ->with('success', 'Company deleted successfully.');
    }
}
