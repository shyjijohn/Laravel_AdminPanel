<?php

namespace Tests\Feature;

use App\Models\Company;
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
}
