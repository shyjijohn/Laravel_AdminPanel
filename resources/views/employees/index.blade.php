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

    <form class="directory-search" method="GET" action="{{ route('employees.index') }}" role="search">
        <div class="directory-search__field">
            <label for="employee-search" class="form-label">Search employees</label>
            <input
                id="employee-search"
                name="search"
                type="search"
                class="form-control"
                value="{{ $search }}"
                maxlength="100"
                placeholder="Name, company, email or phone"
            >
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
        @if ($search !== '')
            <a href="{{ route('employees.index') }}" class="btn btn-secondary">Clear filters</a>
        @endif
    </form>

    @if ($search !== '')
        <p class="directory-results" aria-live="polite">
            {{ $employees->total() }} {{ Str::plural('result', $employees->total()) }} for “{{ $search }}”
        </p>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <x-sortable-heading column="first_name" label="First Name" :sort="$sort" :direction="$direction" />
                            <x-sortable-heading column="last_name" label="Last Name" :sort="$sort" :direction="$direction" />
                            <x-sortable-heading column="company" label="Company" :sort="$sort" :direction="$direction" />
                            <x-sortable-heading column="email" label="Email" :sort="$sort" :direction="$direction" />
                            <x-sortable-heading column="phone" label="Phone" :sort="$sort" :direction="$direction" />
                            <th scope="col" class="text-end">Actions</th>
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
                                <td colspan="6" class="text-center py-4 text-muted">
                                    {{ $search !== '' ? 'No employees match your search.' : 'No employees found.' }}
                                </td>
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
