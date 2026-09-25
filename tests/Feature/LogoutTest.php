<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_logout_returns_guest_to_create_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->get('/')
            ->assertOk()
            ->assertSee('Cerrar sesión');

        Livewire::test('logout-button')
            ->call('logout')
            ->assertRedirect(route('home'));

        $this->assertGuest();

        $this->get('/')
            ->assertOk()
            ->assertSee('Crear cuenta')
            ->assertSee('Iniciar sesión')
            ->assertDontSee('Confirmar contraseña')
            ->assertSee('Dale un trabajo a cada')
            ->assertDontSee('Dinero por asignar');
    }
}
