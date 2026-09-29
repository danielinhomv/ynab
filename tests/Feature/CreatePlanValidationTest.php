<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CreatePlanValidationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_empty_name_shows_the_field_error_and_does_not_create_a_plan(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test('pages::create-plan')
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name' => 'required'])
            ->assertSee('El nombre es obligatorio.');

        $this->assertSame(0, Plan::query()->count());
    }
}
