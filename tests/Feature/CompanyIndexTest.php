<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_companies_are_ordered_alphabetically_by_name(): void
    {
        $user = User::factory()->create();

        foreach (['Zenith Studio', 'Beacon Works', 'Northstar Digital', 'Acorn Group'] as $index => $name) {
            Company::create([
                'name' => $name,
                'email' => "company{$index}@example.com",
            ]);
        }

        $this->actingAs($user)
            ->get(route('companies.index'))
            ->assertOk()
            ->assertSeeTextInOrder([
                'Acorn Group',
                'Beacon Works',
                'Northstar Digital',
                'Zenith Studio',
            ]);
    }

    public function test_companies_can_be_searched_by_name_email_and_website(): void
    {
        $user = User::factory()->create();

        Company::create([
            'name' => 'Northstar Digital',
            'email' => 'hello@northstar.example',
            'website' => 'https://northstar.example/services',
        ]);
        Company::create([
            'name' => 'Harbour Systems',
            'email' => 'contact@harbour.example',
            'website' => 'https://harbour.example/platform',
        ]);

        foreach (['Northstar', 'hello@northstar', '/services'] as $term) {
            $this->actingAs($user)
                ->get(route('companies.index', ['search' => $term]))
                ->assertOk()
                ->assertSeeText('Northstar Digital')
                ->assertDontSeeText('Harbour Systems');
        }

        $this->actingAs($user)
            ->get(route('companies.index', ['search' => 'no-match-value']))
            ->assertOk()
            ->assertSeeText('No companies match your search.')
            ->assertSee('value="no-match-value"', false);
    }

    public function test_companies_can_be_sorted_by_each_supported_column_in_both_directions(): void
    {
        $user = User::factory()->create();

        Company::create([
            'name' => 'Zenith Studio',
            'email' => 'alpha@example.com',
            'website' => 'https://middle.example',
        ]);
        Company::create([
            'name' => 'Acorn Group',
            'email' => 'middle@example.com',
            'website' => 'https://zenith.example',
        ]);
        Company::create([
            'name' => 'Middle Works',
            'email' => 'zenith@example.com',
            'website' => 'https://alpha.example',
        ]);

        $expectedAscending = [
            'name' => ['Acorn Group', 'Middle Works', 'Zenith Studio'],
            'email' => ['Zenith Studio', 'Acorn Group', 'Middle Works'],
            'website' => ['Middle Works', 'Zenith Studio', 'Acorn Group'],
        ];

        foreach ($expectedAscending as $column => $companies) {
            $this->actingAs($user)
                ->get(route('companies.index', ['sort' => $column, 'direction' => 'asc']))
                ->assertOk()
                ->assertSeeTextInOrder($companies);

            $this->actingAs($user)
                ->get(route('companies.index', ['sort' => $column, 'direction' => 'desc']))
                ->assertOk()
                ->assertSeeTextInOrder(array_reverse($companies));
        }
    }

    public function test_invalid_company_sort_parameters_are_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('companies.index'))
            ->get(route('companies.index', ['sort' => 'password', 'direction' => 'sideways']))
            ->assertRedirect(route('companies.index'))
            ->assertSessionHasErrors(['sort', 'direction']);
    }

    public function test_company_filters_and_sorting_are_preserved_in_pagination_links(): void
    {
        $user = User::factory()->create();

        for ($index = 1; $index <= 11; $index++) {
            Company::create([
                'name' => "Example Company {$index}",
                'email' => "company{$index}@example.com",
                'website' => "https://company{$index}.example",
            ]);
        }

        $this->actingAs($user)
            ->get(route('companies.index', [
                'search' => 'Example',
                'sort' => 'email',
                'direction' => 'desc',
            ]))
            ->assertOk()
            ->assertSee('search=Example', false)
            ->assertSee('sort=email', false)
            ->assertSee('direction=desc', false)
            ->assertSee('page=2', false);
    }

    public function test_company_tables_have_accessible_responsive_scroll_regions(): void
    {
        $user = User::factory()->create();
        $company = Company::create([
            'name' => 'Responsive Company',
            'email' => 'responsive@example.com',
        ]);
        Employee::create([
            'first_name' => 'Responsive',
            'last_name' => 'Employee',
            'company_id' => $company->id,
            'email' => 'employee@example.com',
        ]);

        $this->actingAs($user)
            ->get(route('companies.index'))
            ->assertOk()
            ->assertSee('class="table-responsive directory-table-wrap"', false)
            ->assertSee('aria-label="Company directory table"', false)
            ->assertSee('directory-table company-table', false);

        $this->actingAs($user)
            ->get(route('companies.show', $company))
            ->assertOk()
            ->assertSee('tabindex="0"', false)
            ->assertSee('role="region"', false)
            ->assertSee('aria-label="Employees at Responsive Company"', false)
            ->assertSee('directory-table company-employees-table', false);
    }
}
