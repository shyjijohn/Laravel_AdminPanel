<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_employees_are_ordered_by_surname_then_first_name(): void
    {
        $user = User::factory()->create();
        $company = Company::create([
            'name' => 'Test Company',
            'email' => 'company@example.com',
        ]);

        foreach ([
            ['first_name' => 'Zoe', 'last_name' => 'Brown'],
            ['first_name' => 'Adam', 'last_name' => 'Clark'],
            ['first_name' => 'Amy', 'last_name' => 'Brown'],
            ['first_name' => 'Liam', 'last_name' => 'Anderson'],
        ] as $index => $name) {
            Employee::create($name + [
                'company_id' => $company->id,
                'email' => "employee{$index}@example.com",
            ]);
        }

        $this->actingAs($user)
            ->get(route('employees.index'))
            ->assertOk()
            ->assertSeeTextInOrder([
                'Liam', 'Anderson',
                'Amy', 'Brown',
                'Zoe', 'Brown',
                'Adam', 'Clark',
            ]);
    }

    public function test_employees_can_be_searched_across_all_supported_fields(): void
    {
        $user = User::factory()->create();
        $alphaCompany = Company::create([
            'name' => 'Northstar Digital',
            'email' => 'northstar@example.com',
        ]);
        $betaCompany = Company::create([
            'name' => 'Harbour Systems',
            'email' => 'harbour@example.com',
        ]);

        Employee::create([
            'first_name' => 'Amelia',
            'last_name' => 'Hart',
            'company_id' => $alphaCompany->id,
            'email' => 'amelia.hart@example.com',
            'phone' => '020 7946 0101',
        ]);
        Employee::create([
            'first_name' => 'Benjamin',
            'last_name' => 'Cole',
            'company_id' => $betaCompany->id,
            'email' => 'ben.cole@example.net',
            'phone' => '0161 555 0182',
        ]);

        foreach (['Amelia', 'Hart', 'amelia.hart@', '7946', 'Northstar'] as $term) {
            $this->actingAs($user)
                ->get(route('employees.index', ['search' => $term]))
                ->assertOk()
                ->assertSeeText('Amelia')
                ->assertDontSeeText('Benjamin');
        }

        $this->actingAs($user)
            ->get(route('employees.index', ['search' => 'no-match-value']))
            ->assertOk()
            ->assertSeeText('No employees match your search.')
            ->assertSee('value="no-match-value"', false);
    }

    public function test_employees_can_be_sorted_by_each_supported_column_in_both_directions(): void
    {
        $user = User::factory()->create();
        $alpha = Company::create(['name' => 'Alpha Ltd', 'email' => 'alpha@example.com']);
        $middle = Company::create(['name' => 'Middle Ltd', 'email' => 'middle@example.com']);
        $zenith = Company::create(['name' => 'Zenith Ltd', 'email' => 'zenith@example.com']);

        Employee::create([
            'first_name' => 'Zoe', 'last_name' => 'Brown', 'company_id' => $zenith->id,
            'email' => 'zoe@example.com', 'phone' => '300',
        ]);
        Employee::create([
            'first_name' => 'Amy', 'last_name' => 'Clark', 'company_id' => $alpha->id,
            'email' => 'amy@example.com', 'phone' => '100',
        ]);
        Employee::create([
            'first_name' => 'Liam', 'last_name' => 'Anderson', 'company_id' => $middle->id,
            'email' => 'liam@example.com', 'phone' => '200',
        ]);

        $expectedAscending = [
            'first_name' => ['Amy', 'Liam', 'Zoe'],
            'last_name' => ['Liam', 'Zoe', 'Amy'],
            'company' => ['Amy', 'Liam', 'Zoe'],
            'email' => ['Amy', 'Liam', 'Zoe'],
            'phone' => ['Amy', 'Liam', 'Zoe'],
        ];

        foreach ($expectedAscending as $column => $names) {
            $this->actingAs($user)
                ->get(route('employees.index', ['sort' => $column, 'direction' => 'asc']))
                ->assertOk()
                ->assertSeeTextInOrder($names);

            $this->actingAs($user)
                ->get(route('employees.index', ['sort' => $column, 'direction' => 'desc']))
                ->assertOk()
                ->assertSeeTextInOrder(array_reverse($names));
        }
    }

    public function test_invalid_employee_sort_parameters_are_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('employees.index'))
            ->get(route('employees.index', ['sort' => 'password', 'direction' => 'sideways']))
            ->assertRedirect(route('employees.index'))
            ->assertSessionHasErrors(['sort', 'direction']);
    }

    public function test_employee_filters_and_sorting_are_preserved_in_pagination_links(): void
    {
        $user = User::factory()->create();
        $company = Company::create(['name' => 'Test Company', 'email' => 'test@example.com']);

        for ($index = 1; $index <= 11; $index++) {
            Employee::create([
                'first_name' => "Person {$index}",
                'last_name' => 'Tester',
                'company_id' => $company->id,
                'email' => "person{$index}@example.com",
            ]);
        }

        $this->actingAs($user)
            ->get(route('employees.index', [
                'search' => 'Tester',
                'sort' => 'first_name',
                'direction' => 'desc',
            ]))
            ->assertOk()
            ->assertSee('search=Tester', false)
            ->assertSee('sort=first_name', false)
            ->assertSee('direction=desc', false)
            ->assertSee('page=2', false);
    }
}
