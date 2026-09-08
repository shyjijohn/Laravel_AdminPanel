@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <p class="page-eyebrow">Directory</p>
            <h1 class="page-title">Companies</h1>
            <p class="page-description">Manage company records and their contact details.</p>
        </div>
        <a href="{{ route('companies.create') }}" class="btn btn-primary">Add New Company</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form class="directory-search" method="GET" action="{{ route('companies.index') }}" role="search">
        <div class="directory-search__field">
            <label for="company-search" class="form-label">Search companies</label>
            <input
                id="company-search"
                name="search"
                type="search"
                class="form-control"
                value="{{ $search }}"
                maxlength="100"
                placeholder="Company name, email or website"
            >
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
        @if ($search !== '')
            <a href="{{ route('companies.index') }}" class="btn btn-secondary">Clear filters</a>
        @endif
    </form>

    @if ($search !== '')
        <p class="directory-results" aria-live="polite">
            {{ $companies->total() }} {{ Str::plural('result', $companies->total()) }} for “{{ $search }}”
        </p>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive directory-table-wrap" tabindex="0" role="region" aria-label="Company directory table">
                <table class="table table-hover align-middle mb-0 directory-table company-table">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Logo</th>
                            <x-sortable-heading column="name" label="Name" :sort="$sort" :direction="$direction" />
                            <x-sortable-heading column="email" label="Email" :sort="$sort" :direction="$direction" />
                            <x-sortable-heading column="website" label="Website" :sort="$sort" :direction="$direction" />
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($companies as $company)
                            <tr>
                                <td>
                                    @if ($company->logo)
                                        <img src="{{ asset($company->logo) }}" alt="{{ $company->name }}" class="img-thumbnail company-table__logo">
                                    @else
                                        <span class="badge bg-secondary">No Logo</span>
                                    @endif
                                </td>
                                <td><strong>{{ $company->name }}</strong></td>
                                <td>{{ $company->email ?? 'N/A' }}</td>
                                <td>
                                    @if ($company->website)
                                        <a href="{{ $company->website }}" target="_blank" rel="noopener noreferrer">{{ $company->website }}</a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <div class="table-actions">
                                    <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-action-view">View</a>
                                    <a href="{{ route('companies.edit', $company) }}" class="btn btn-sm btn-action-edit">Edit</a>
                                    @if ($company->employees_count > 0)
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-action-delete"
                                            disabled
                                            title="Reassign or delete this company’s employees before deleting the company."
                                        >Delete</button>
                                    @else
                                        <form action="{{ route('companies.destroy', $company) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this company?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-action-delete">Delete</button>
                                        </form>
                                    @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    {{ $search !== '' ? 'No companies match your search.' : 'No companies found.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($companies->hasPages())
            <div class="card-footer d-flex justify-content-end">
                {{ $companies->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
