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
}
