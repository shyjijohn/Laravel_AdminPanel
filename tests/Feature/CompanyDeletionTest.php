<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_with_employees_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $company = Company::create([
            'name' => 'Protected Company',
            'email' => 'protected@example.com',
            'logo' => 'logos/protected.png',
        ]);
        $employee = Employee::create([
            'first_name' => 'Assigned',
            'last_name' => 'Employee',
            'company_id' => $company->id,
            'email' => 'assigned@example.com',
        ]);

        $this->actingAs($user)
            ->delete(route('companies.destroy', $company))
            ->assertRedirect(route('companies.index'))
            ->assertSessionHas('error', 'This company cannot be deleted while employees are assigned to it. Reassign or delete those employees first.');

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'logo' => 'logos/protected.png',
        ]);
        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'company_id' => $company->id,
        ]);
    }

    public function test_database_restricts_direct_deletion_of_company_with_employees(): void
    {
        $company = Company::create([
            'name' => 'Database Protected Company',
            'email' => 'database-protected@example.com',
        ]);
        Employee::create([
            'first_name' => 'Assigned',
            'last_name' => 'Employee',
            'company_id' => $company->id,
            'email' => 'database-assigned@example.com',
        ]);

        $this->expectException(QueryException::class);

        $company->delete();
    }

    public function test_company_without_employees_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $company = Company::create([
            'name' => 'Empty Company',
            'email' => 'empty@example.com',
        ]);

        $this->actingAs($user)
            ->delete(route('companies.destroy', $company))
            ->assertRedirect(route('companies.index'))
            ->assertSessionHas('success', 'Company deleted successfully.');

        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    }

    public function test_company_index_disables_delete_when_employees_are_assigned(): void
    {
        $user = User::factory()->create();
        $company = Company::create([
            'name' => 'Assigned Company',
            'email' => 'assigned-company@example.com',
        ]);
        Employee::create([
            'first_name' => 'Assigned',
            'last_name' => 'Employee',
            'company_id' => $company->id,
            'email' => 'ui-assigned@example.com',
        ]);

        $this->actingAs($user)
            ->get(route('companies.index'))
            ->assertOk()
            ->assertSee('disabled', false)
            ->assertSee('Reassign or delete this company’s employees before deleting the company.', false);
    }
}
