@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <p class="page-eyebrow">Directory</p>
            <h1 class="page-title">Employees</h1>
            <p class="page-description">Manage employee records and company assignments.</p>
        </div>
        <a href="{{ route('employees.create') }}" class="btn btn-primary">Add New Employee</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Company</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employees as $employee)
                            <tr>
                                <td>{{ $employee->first_name }}</td>
                                <td>{{ $employee->last_name }}</td>
                                <td>
                                    @if ($employee->company)
                                        <a href="{{ route('companies.show', $employee->company) }}">
                                            {{ $employee->company->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">Unassigned</span>
                                    @endif
                                </td>
                                <td>{{ $employee->email ?? 'N/A' }}</td>
                                <td>{{ $employee->phone ?? 'N/A' }}</td>
                                <td>
                                    <div class="table-actions">
                                    <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-action-view">View</a>
                                    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-action-edit">Edit</a>
                                    <form action="{{ route('employees.destroy', $employee) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-action-delete">Delete</button>
                                    </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No employees found.</td>
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
