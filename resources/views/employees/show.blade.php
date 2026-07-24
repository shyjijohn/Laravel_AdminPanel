@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Employee Profile</h2>
        <div>
            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning me-1">Edit Employee</a>
            <a href="{{ route('employees.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h4 class="mb-0">{{ $employee->first_name }} {{ $employee->last_name }}</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Company:</div>
                        <div class="col-sm-8">
                            @if ($employee->company)
                                <a href="{{ route('companies.show', $employee->company) }}">
                                    {{ $employee->company->name }}
                                </a>
                            @else
                                <span class="text-muted">Unassigned</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Email Address:</div>
                        <div class="col-sm-8">{{ $employee->email ?? 'N/A' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Phone Number:</div>
                        <div class="col-sm-8">{{ $employee->phone ?? 'N/A' }}</div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 fw-bold">Created At:</div>
                        <div class="col-sm-8">{{ $employee->created_at->format('M d, Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection