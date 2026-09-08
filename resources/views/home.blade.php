@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <p class="page-eyebrow">Overview</p>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-description">Your central place for managing companies and employees.</p>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <section class="dashboard-welcome" aria-labelledby="welcome-heading">
        <div class="dashboard-welcome__content">
            <p class="dashboard-welcome__label">Welcome back</p>
            <h2 id="welcome-heading">Hello, {{ Auth::user()->name }}!</h2>
            <p>You’re signed in and ready to manage your organisation’s company and employee records.</p>
        </div>
        <div class="dashboard-welcome__mark" aria-hidden="true">AP</div>
    </section>

    <section class="dashboard-stats" aria-label="Record totals">
        <article class="dashboard-stat">
            <p class="dashboard-stat__label">Companies</p>
            <p class="dashboard-stat__value">{{ number_format($companyCount) }}</p>
            <p class="dashboard-stat__description">Company records in the directory</p>
        </article>
        <article class="dashboard-stat">
            <p class="dashboard-stat__label">Employees</p>
            <p class="dashboard-stat__value">{{ number_format($employeeCount) }}</p>
            <p class="dashboard-stat__description">Employee records across all companies</p>
        </article>
    </section>

    <nav class="dashboard-shortcuts" aria-label="Management shortcuts">
        <a class="dashboard-shortcut" href="{{ route('companies.index') }}">
            <span class="dashboard-shortcut__icon" aria-hidden="true">C</span>
            <span>
                <strong>Manage Companies</strong>
                <small>View and maintain company records</small>
            </span>
            <span class="dashboard-shortcut__arrow" aria-hidden="true">&rarr;</span>
        </a>
        <a class="dashboard-shortcut" href="{{ route('employees.index') }}">
            <span class="dashboard-shortcut__icon" aria-hidden="true">E</span>
            <span>
                <strong>Manage Employees</strong>
                <small>View and maintain employee records</small>
            </span>
            <span class="dashboard-shortcut__arrow" aria-hidden="true">&rarr;</span>
        </a>
    </nav>

    <section class="row g-4" aria-label="Recently added records">
        <div class="col-12 col-xl-6">
            <div class="card dashboard-list h-100">
                <div class="card-header">
                    <div>
                        <p class="dashboard-list__eyebrow">Latest activity</p>
                        <h2 class="dashboard-list__title">Recent Companies</h2>
                    </div>
                    <a href="{{ route('companies.index') }}" class="dashboard-list__link">View all</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Company</th>
                                <th scope="col">Added</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentCompanies as $company)
                                <tr>
                                    <td>
                                        <strong>{{ $company->name }}</strong>
                                        <span class="dashboard-list__secondary">{{ $company->email }}</span>
                                    </td>
                                    <td class="text-nowrap">{{ $company->created_at->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center py-4 text-muted">No companies have been added yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card dashboard-list h-100">
                <div class="card-header">
                    <div>
                        <p class="dashboard-list__eyebrow">Latest activity</p>
                        <h2 class="dashboard-list__title">Recent Employees</h2>
                    </div>
                    <a href="{{ route('employees.index') }}" class="dashboard-list__link">View all</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Employee</th>
                                <th scope="col">Company</th>
                                <th scope="col">Added</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentEmployees as $employee)
                                <tr>
                                    <td>
                                        <strong>{{ $employee->first_name }} {{ $employee->last_name }}</strong>
                                        <span class="dashboard-list__secondary">{{ $employee->email ?? 'No email' }}</span>
                                    </td>
                                    <td>{{ $employee->company?->name ?? 'Unassigned' }}</td>
                                    <td class="text-nowrap">{{ $employee->created_at->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">No employees have been added yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
