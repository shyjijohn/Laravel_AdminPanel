@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-end mt-3">
    {{ $companies->links() }}
        <a href="{{ route('companies.create') }}" class="btn btn-primary">Add New Company</a>
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
                            <th>Logo</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Website</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($companies as $company)
                            <tr>
                                <td style="width: 80px;">
                                    @if ($company->logo)
                                        <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}" class="img-thumbnail" style="max-height: 50px;">
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
                                <td class="text-end">
                                    <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-outline-info me-1">View</a>
                                    <a href="{{ route('companies.edit', $company) }}" class="btn btn-sm btn-outline-warning me-1">Edit</a>
                                    <form action="{{ route('companies.destroy', $company) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this company?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No companies found.</td>
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