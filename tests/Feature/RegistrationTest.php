<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_sees_create_account_fields_on_home(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Nombre');
        $response->assertSee('Correo electrónico');
        $response->assertSee('Contraseña');
        $response->assertSee('Confirmar contraseña');
        $response->assertSee('Dale un trabajo a cada');
        $response->assertDontSee('Dinero por asignar');
        $response->assertDontSee('Presupuesto Mensual');
    }

    public function test_valid_registration_authenticates_and_shows_dashboard(): void
    {
        Livewire::test('pages::register')
            ->set('name', 'Ana Pérez')
            ->set('email', 'ana@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('register')
            ->assertRedirect(route('home'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'ana@example.com',
            'name' => 'Ana Pérez',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Cerrar sesión')
            ->assertSee('Dinero por asignar')
            ->assertDontSee('Confirmar contraseña')
            ->assertDontSee('Dale un trabajo a cada');
    }

    public function test_mismatched_passwords_do_not_create_a_user(): void
    {
        Livewire::test('pages::register')
            ->set('name', 'Ana Pérez')
            ->set('email', 'ana@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'other-password')
            ->call('register')
            ->assertHasErrors(['password']);

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'ana@example.com']);
    }

    public function test_duplicate_email_does_not_create_another_user(): void
    {
        User::factory()->create(['email' => 'ana@example.com']);

        Livewire::test('pages::register')
            ->set('name', 'Otra Persona')
            ->set('email', 'ana@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('register')
            ->assertHasErrors(['email']);

        $this->assertGuest();
        $this->assertSame(1, User::query()->where('email', 'ana@example.com')->count());
    }
}
