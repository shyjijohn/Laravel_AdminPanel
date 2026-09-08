<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Employee;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $companyCount = Company::count();
        $employeeCount = Employee::count();
        $recentCompanies = Company::latest()->limit(5)->get();
        $recentEmployees = Employee::with('company')->latest()->limit(5)->get();

        return view('home', compact(
            'companyCount',
            'employeeCount',
            'recentCompanies',
            'recentEmployees'
        ));
    }
}
