<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_create_account_page_links_to_login(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(route('login'), false);
    }

    public function test_login_page_links_to_create_account(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('Correo electrónico');
        $response->assertSee('Contraseña');
        $response->assertSee('Dale un trabajo a cada');
        $response->assertSee(route('home'), false);
        $response->assertDontSee('Dinero por asignar');
        $response->assertDontSee('Presupuesto Mensual');
    }

    public function test_valid_credentials_authenticate(): void
    {
        $user = User::factory()->create([
            'email' => 'ana@example.com',
        ]);

        Livewire::test('pages::login')
            ->set('email', 'ana@example.com')
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_credentials_do_not_authenticate(): void
    {
        User::factory()->create([
            'email' => 'ana@example.com',
        ]);

        Livewire::test('pages::login')
            ->set('email', 'ana@example.com')
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }
}
