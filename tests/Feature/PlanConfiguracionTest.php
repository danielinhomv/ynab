<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Cuenta;
use App\Models\Plan;
use App\Models\Sobre;
use App\Models\SobreMes;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PlanConfiguracionTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_open_plan_links_in_one_click_to_its_five_saved_values(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->for($user)->create([
            'name' => 'Casa',
            'currency' => 'USD',
            'fecha' => '2026-03-15',
            'number_format' => '1,234.56',
            'currency_placement' => 'después',
        ]);
        $url = route('plan.configuracion', $plan);

        $this->actingAs($user);

        Livewire::test('plan-sobres')
            ->assertSee('Moneda del plan: Bolivianos (Bs.)')
            ->assertSeeHtml('href="'.$url.'"');

        $this->get('/')
            ->assertOk()
            ->assertSeeHtml('href="'.$url.'"')
            ->assertSee('Plan activo')
            ->assertSee('Configuración');

        $this->get($url)
            ->assertOk()
            ->assertSee('Configuración del plan')
            ->assertDontSee('Configuración de cuenta de usuario')
            ->assertDontSee('Correo electrónico');

        Livewire::test('pages::plan-configuracion', ['plan' => $plan])
            ->assertSet('name', 'Casa')
            ->assertSet('currency', 'USD')
            ->assertSet('fecha', '2026-03-15')
            ->assertSet('numberFormat', '1,234.56')
            ->assertSet('currencyPlacement', 'después');

        $this->get(route('configuracion'))
            ->assertOk()
            ->assertSee('Configuración de cuenta de usuario');
    }

    public function test_link_is_absent_until_a_plan_is_open_and_follows_the_opened_plan(): void
    {
        $user = User::factory()->create();
        $older = Plan::factory()->for($user)->create();
        $latest = Plan::factory()->for($user)->create();

        $this->actingAs($user);

        Livewire::test('plan-sobres')
            ->assertSee('Moneda del plan: Bolivianos (Bs.)')
            ->assertSeeHtml('href="'.route('plan.configuracion', $latest).'"')
            ->assertDontSeeHtml(route('plan.configuracion', $older))
            ->call('abrirPlan', $older->id)
            ->assertSeeHtml('href="'.route('plan.configuracion', $older).'"')
            ->assertDontSeeHtml(route('plan.configuracion', $latest));
    }

    public function test_guest_is_redirected_and_foreign_plan_is_not_found(): void
    {
        $user = User::factory()->create();
        $foreign = Plan::factory()->for(User::factory())->create(['name' => 'Plan ajeno']);

        $this->get(route('plan.configuracion', $foreign))->assertRedirect(route('login'));

        $this->actingAs($user)
            ->get(route('plan.configuracion', $foreign))
            ->assertNotFound();

        $this->assertSame('Plan ajeno', $foreign->fresh()->name);
    }

    public function test_valid_save_persists_and_confirms_while_empty_name_saves_nothing(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->for($user)->create([
            'name' => 'Casa',
            'currency' => 'BOB',
            'fecha' => '2026-01-01',
            'number_format' => '1.234,56',
            'currency_placement' => 'antes',
        ]);

        $this->actingAs($user);

        Livewire::test('pages::plan-configuracion', ['plan' => $plan])
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name'])
            ->assertSee('El nombre es obligatorio.')
            ->assertDontSee('Configuración guardada exitosamente');

        $this->assertSame('Casa', $plan->fresh()->name);

        Livewire::test('pages::plan-configuracion', ['plan' => $plan])
            ->set('numberFormat', '1 234,56')
            ->call('save')
            ->assertSee('El valor no es válido.');

        $this->assertSame('1.234,56', $plan->fresh()->number_format);

        Livewire::test('pages::plan-configuracion', ['plan' => $plan])
            ->set('name', 'Casa nueva')
            ->set('fecha', '2026-05-20')
            ->set('numberFormat', '1,234.56')
            ->set('currencyPlacement', 'después')
            ->call('save')
            ->assertHasNoErrors()
            ->assertNoRedirect()
            ->assertSee('Configuración guardada exitosamente');

        $plan->refresh();

        $this->assertSame('Casa nueva', $plan->name);
        $this->assertSame('2026-05-20', $plan->fecha->toDateString());
        $this->assertSame('1,234.56', $plan->number_format);
        $this->assertSame('después', $plan->currency_placement);
        $this->assertSame(1, Plan::query()->where('user_id', $user->id)->count());
    }

    public function test_currency_is_replaced_without_amounts_and_locked_with_amounts(): void
    {
        $user = User::factory()->create();
        $validValues = [
            'currency' => 'BOB',
            'fecha' => '2026-01-01',
            'number_format' => '1.234,56',
            'currency_placement' => 'antes',
        ];
        $empty = Plan::factory()->for($user)->create($validValues);

        $this->actingAs($user);

        Livewire::test('pages::plan-configuracion', ['plan' => $empty])
            ->assertDontSeeHtml('wire:model="currency" disabled')
            ->set('currency', 'EUR')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('EUR', $empty->fresh()->currency);
        $this->assertSame(1, Plan::query()->where('user_id', $user->id)->count());
        $withAmounts = Plan::factory()->for($user)->create([...$validValues, 'name' => 'Con montos']);
        $cuenta = Cuenta::factory()->for($withAmounts)->create(['balance' => '1250.50']);
        $sobre = Sobre::factory()->for(Category::factory()->for($withAmounts))->create(['assigned' => '40.00']);
        $mes = SobreMes::factory()->for($sobre)->create(['assigned' => '80.25']);

        Livewire::test('pages::plan-configuracion', ['plan' => $withAmounts])
            ->assertSeeHtml('wire:model="currency" disabled')
            ->set('currency', 'USD')
            ->set('name', 'Con montos editado')
            ->call('save')
            ->assertSee('Configuración guardada exitosamente');

        $withAmounts->refresh();

        $this->assertSame('BOB', $withAmounts->currency);
        $this->assertSame('Con montos editado', $withAmounts->name);
        $this->assertSame('1250.50', $cuenta->fresh()->balance);
        $this->assertSame('40.00', $sobre->fresh()->assigned);
        $this->assertSame('80.25', $mes->fresh()->assigned);
    }
}
