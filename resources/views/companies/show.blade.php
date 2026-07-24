@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Company Details</h2>
        <div>
            <a href="{{ route('companies.edit', $company) }}" class="btn btn-warning me-1">Edit Company</a>
            <a href="{{ route('companies.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    <!-- Company Details Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-2 text-center mb-3 mb-md-0">
                    @if ($company->logo)
                        <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}" class="img-fluid rounded border" style="max-height: 120px;">
                    @else
                        <div class="p-4 bg-light text-muted border rounded">No Logo</div>
                    @endif
                </div>
                <div class="col-md-10">
                    <h3 class="mb-1">{{ $company->name }}</h3>
                    <p class="mb-1"><strong>Email:</strong> {{ $company->email ?? 'N/A' }}</p>
                    <p class="mb-0">
                        <strong>Website:</strong> 
                        @if ($company->website)
                            <a href="{{ $company->website }}" target="_blank" rel="noopener noreferrer">{{ $company->website }}</a>
                        @else
                            N/A
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Company Employees List -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Employees at {{ $company->name }}</h5>
            <a href="{{ route('employees.create', ['company_id' => $company->id]) }}" class="btn btn-sm btn-primary">Add Employee</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employees as $employee)
                            <tr>
                                <td>{{ $employee->first_name }}</td>
                                <td>{{ $employee->last_name }}</td>
                                <td>{{ $employee->email ?? 'N/A' }}</td>
                                <td>{{ $employee->phone ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No employees assigned to this company.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($employees->hasPages())
            <div class="card-footer d-flex justify-content-end">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
</div>
@endsection