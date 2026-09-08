<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_greets_the_authenticated_user_by_name(): void
    {
        $user = User::factory()->create(['name' => 'Shyji John']);

        $response = $this->actingAs($user)->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('Welcome back')
            ->assertSee('Hello, Shyji John!')
            ->assertSee('Dashboard');
    }

    public function test_guest_cannot_view_the_dashboard(): void
    {
        $this->get(route('home'))->assertRedirect(route('login'));
    }

    public function test_dashboard_links_to_company_and_employee_sections(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Manage Companies')
            ->assertSee('Manage Employees')
            ->assertSee('href="'.route('companies.index').'"', false)
            ->assertSee('href="'.route('employees.index').'"', false);
    }

    public function test_dashboard_displays_totals_and_five_most_recent_records(): void
    {
        $user = User::factory()->create();

        for ($index = 1; $index <= 6; $index++) {
            $company = Company::create([
                'name' => "Company {$index}",
                'email' => "company{$index}@example.com",
            ]);
            $company->forceFill(['created_at' => now()->subDays(6 - $index)])->save();

            $employee = Employee::create([
                'first_name' => "Employee {$index}",
                'last_name' => 'Tester',
                'company_id' => $company->id,
                'email' => "employee{$index}@example.com",
            ]);
            $employee->forceFill(['created_at' => now()->subDays(6 - $index)])->save();
        }

        $response = $this->actingAs($user)->get(route('home'));

        $response
            ->assertOk()
            ->assertSeeText('Company records in the directory')
            ->assertSeeText('Employee records across all companies')
            ->assertSeeTextInOrder(['Company 6', 'Company 5', 'Company 4', 'Company 3', 'Company 2'])
            ->assertSeeTextInOrder(['Employee 6 Tester', 'Employee 5 Tester', 'Employee 4 Tester', 'Employee 3 Tester', 'Employee 2 Tester'])
            ->assertDontSeeText('Company 1')
            ->assertDontSeeText('Employee 1 Tester');
    }
}
