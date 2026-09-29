<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Plan;
use App\Models\Sobre;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CreatePlanTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_authenticated_user_sees_the_five_fields_and_guest_cannot_create(): void
    {
        $this->get(route('planes.crear'))
            ->assertRedirect(route('login'));

        $this->assertSame(0, Plan::query()->count());

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('planes.crear'))
            ->assertOk()
            ->assertSee('Nombre')
            ->assertSee('Multimoneda')
            ->assertSee('Fecha')
            ->assertSee('Number format')
            ->assertSee('Currency placement')
            ->assertSee('BOB')
            ->assertSee('1.234,56')
            ->assertSee('antes');
    }

    public function test_plan_stores_one_currency_and_another_user_cannot_see_it(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($user);

        Livewire::test('pages::create-plan')
            ->set('name', 'Casa')
            ->set('currency', 'USD')
            ->set('fecha', '2026-09-28')
            ->set('numberFormat', '1,234.56')
            ->set('currencyPlacement', 'después')
            ->call('save')
            ->assertHasNoErrors();

        $plan = Plan::query()->where('name', 'Casa')->first();

        $this->assertNotNull($plan);
        $this->assertSame($user->id, $plan->user_id);
        $this->assertSame('USD', $plan->currency);
        $this->assertSame('2026-09-28', $plan->fecha->toDateString());
        $this->assertSame('1,234.56', $plan->number_format);
        $this->assertSame('después', $plan->currency_placement);
        $this->assertFalse($other->plans()->whereKey($plan->id)->exists());

        $this->actingAs($other)
            ->get(route('planes.show', $plan))
            ->assertNotFound();
    }

    public function test_name_alone_creates_the_plan_with_the_visible_defaults(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test('pages::create-plan')
            ->assertSet('currency', 'BOB')
            ->assertSet('fecha', now()->toDateString())
            ->assertSet('numberFormat', '1.234,56')
            ->assertSet('currencyPlacement', 'antes')
            ->set('name', 'Casa')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $plan = $user->plans()->first();

        $this->assertNotNull($plan);
        $this->assertSame('BOB', $plan->currency);
        $this->assertSame(now()->toDateString(), $plan->fecha->toDateString());
        $this->assertSame('1.234,56', $plan->number_format);
        $this->assertSame('antes', $plan->currency_placement);
    }

    public function test_valid_create_stores_the_plan_without_categories_and_invalid_create_stores_nothing(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test('pages::create-plan')
            ->set('name', 'Casa')
            ->set('currency', 'GBP')
            ->call('save')
            ->assertHasErrors(['currency'])
            ->assertSee('El valor no es válido.');

        $this->assertSame(0, Plan::query()->count());
        $this->assertSame(0, Category::query()->count());
        $this->assertSame(0, Sobre::query()->count());

        Livewire::test('pages::create-plan')
            ->set('name', 'Casa')
            ->call('save')
            ->assertHasNoErrors();

        $plan = $user->plans()->first();

        $this->assertNotNull($plan);
        $this->assertSame(0, $plan->categories()->count());
        $this->assertSame(0, Sobre::query()->count());
    }

    public function test_successful_create_shows_confirmation_and_a_second_plan_is_allowed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test('pages::create-plan')
            ->set('name', 'Casa')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $first = $user->plans()->where('name', 'Casa')->first();

        $this->get(route('planes.show', $first))
            ->assertOk()
            ->assertSee('Plan creado exitosamente')
            ->assertSee('Casa')
            ->assertDontSee('Servicios básicos');

        Livewire::test('pages::create-plan')
            ->set('name', 'Viaje')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(2, $user->plans()->count());
        $this->assertTrue($user->plans()->where('name', 'Viaje')->exists());
    }
}
