<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AccountSettingsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_authenticated_user_updates_name_and_email(): void
    {
        $user = User::factory()->create([
            'name' => 'Ana Pérez',
            'email' => 'ana@example.com',
        ]);

        $this->actingAs($user);

        Livewire::test('pages::account-settings')
            ->set('name', 'Ana García')
            ->set('email', 'ana.garcia@example.com')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Ana García',
            'email' => 'ana.garcia@example.com',
        ]);
    }

    public function test_guest_cannot_view_account_settings(): void
    {
        $this->get(route('configuracion'))
            ->assertRedirect(route('login'));
    }
}
