<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Cuenta;
use App\Models\Gasto;
use App\Models\Plan;
use App\Models\Sobre;
use App\Models\SobreMes;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PlanSobresTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_plan_view_shows_category_and_sobre_without_create_or_foreign_rows(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $plan = Plan::factory()->for($user)->create([
            'currency' => 'BOB',
            'number_format' => '1.234,56',
            'currency_placement' => 'antes',
        ]);
        $category = Category::factory()->for($plan)->create([
            'name' => 'Servicios básicos',
        ]);
        $sobre = Sobre::factory()->for($category)->create([
            'name' => 'Luz',
            'assigned' => '1.00',
            'activity' => '1.00',
            'available' => '1.00',
        ]);
        SobreMes::factory()->for($sobre)->create([
            'anio' => (int) now()->year,
            'mes' => (int) now()->month,
            'assigned' => '280.00',
            'activity' => '40.00',
            'available' => '240.00',
        ]);
        $foreignCategory = Category::factory()->for(Plan::factory()->for($other))->create();
        Sobre::factory()->for($foreignCategory)->create([
            'name' => 'Sobre ajeno',
        ]);

        $this->actingAs($user)->get('/')
            ->assertOk()
            ->assertSee('Servicios básicos')
            ->assertSee('Luz')
            ->assertSee('Bs 280,00')
            ->assertSee('Bs 40,00')
            ->assertSee('Bs 240,00')
            ->assertSee('Asignado')
            ->assertSee('Actividad')
            ->assertSee('Disponible')
            ->assertDontSee('Sobre ajeno')
            ->assertDontSee('Eliminar')
            ->assertDontSee('Cubrir sobregiro');

        Livewire::test('plan-sobres')
            ->assertDontSee('Eliminar')
            ->assertDontSee('+ Nueva categoría')
            ->assertDontSee('+ Nuevo sobre');
    }

    public function test_guest_does_not_see_the_plan_sobres(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->for(Plan::factory()->for($user))->create([
            'name' => 'Servicios básicos',
        ]);
        Sobre::factory()->for($category)->create(['name' => 'Luz']);

        Livewire::test('plan-sobres')->assertDontSee('Luz');

        $this->get('/')->assertDontSee('Servicios básicos');
    }

    public function test_amounts_use_the_plan_format_and_negative_available_is_red(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->for($user)->create([
            'currency' => 'USD',
            'number_format' => '1,234.56',
            'currency_placement' => 'después',
        ]);
        $category = Category::factory()->for($plan)->create(['name' => 'Casa']);
        $sobre = Sobre::factory()->for($category)->create([
            'name' => 'Supermercado',
            'assigned' => '1.00',
            'activity' => '1.00',
            'available' => '-150.00',
        ]);
        SobreMes::factory()->for($sobre)->create([
            'anio' => (int) now()->year,
            'mes' => (int) now()->month,
            'assigned' => '1250.50',
            'activity' => '1400.00',
            'available' => '-150.00',
        ]);

        $this->actingAs($user);

        Livewire::test('plan-sobres')
            ->assertSee('1,250.50 USD')
            ->assertSee('1,400.00 USD')
            ->assertSee('-150.00 USD')
            ->assertSeeHtml('text-red-600')
            ->assertSeeHtml('bg-red-50/70')
            ->assertDontSee('Cubrir sobregiro');

        $this->assertIsString($sobre->fresh()->available);
        $this->assertSame('-150.00', $sobre->fresh()->available);
    }

    public function test_row_edit_saves_a_numeric_amount_and_rejects_an_invalid_one(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $older = Plan::factory()->for($user)->create();
        $latest = Plan::factory()->for($user)->create([
            'currency' => 'BOB',
            'number_format' => '1.234,56',
            'currency_placement' => 'antes',
        ]);
        $category = Category::factory()->for($latest)->create(['name' => 'Casa']);
        $sobre = Sobre::factory()->for($category)->create([
            'name' => 'Luz',
            'assigned' => '10.00',
            'activity' => '1.00',
            'available' => '9.00',
        ]);
        $cuenta = Cuenta::factory()->for($latest)->create([
            'name' => 'Caja',
            'balance' => '50.00',
        ]);
        $olderSobre = Sobre::factory()->for(Category::factory()->for($older))->create([
            'name' => 'Viejo',
            'assigned' => '5.00',
        ]);
        $foreign = Sobre::factory()->for(
            Category::factory()->for(Plan::factory()->for($other))
        )->create([
            'name' => 'Ajeno',
            'assigned' => '8.00',
        ]);

        $this->actingAs($user);

        Livewire::test('plan-sobres')
            ->call('abrirPlan', $latest->id)
            ->assertDontSee('Viejo')
            ->assertSee('Luz')
            ->call('edit', $sobre->id)
            ->assertSet('editingId', $sobre->id)
            ->set('name', 'Luz cocina')
            ->set('assigned', '20,50')
            ->set('gasto', '3,00')
            ->set('gastoCuentaId', $cuenta->id)
            ->set('available', '-2,50')
            ->call('save')
            ->assertSet('editingId', null)
            ->assertNoRedirect();

        $sobre->refresh();
        $mes = $sobre->meses()->first();

        $this->assertNotNull($mes);
        $this->assertSame('Luz cocina', $sobre->name);
        $this->assertSame('10.00', $sobre->assigned);
        $this->assertSame('20.50', $mes->assigned);
        $this->assertSame('3.00', $mes->activity);
        $this->assertSame('17.50', $mes->available);
        $this->assertSame('47.00', $cuenta->fresh()->balance);
        $this->assertSame('3.00', Gasto::query()->where('cuenta_id', $cuenta->id)->value('monto'));

        Livewire::test('plan-sobres')
            ->call('abrirPlan', $latest->id)
            ->call('edit', $sobre->id)
            ->set('assigned', 'abc')
            ->call('save')
            ->assertSee('El valor no es válido.')
            ->assertSet('editingId', $sobre->id);

        $this->assertSame('20.50', $sobre->meses()->first()->assigned);
        $this->assertSame('10.00', $sobre->fresh()->assigned);

        Livewire::test('plan-sobres')
            ->call('edit', $foreign->id)
            ->assertSet('editingId', null)
            ->call('save');

        $this->assertSame('8.00', $foreign->fresh()->assigned);
        $this->assertSame('5.00', $olderSobre->fresh()->assigned);
    }

    public function test_nuevo_gasto_adds_to_the_month_activity(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->for($user)->create([
            'currency' => 'BOB',
            'number_format' => '1.234,56',
            'currency_placement' => 'antes',
        ]);
        $sobre = Sobre::factory()->for(Category::factory()->for($plan))->create(['name' => 'Comida']);
        $cuenta = Cuenta::factory()->for($plan)->create([
            'name' => 'Caja',
            'balance' => '1500.00',
        ]);
        SobreMes::factory()->for($sobre)->create([
            'anio' => (int) now()->year,
            'mes' => (int) now()->month,
            'assigned' => '2000.00',
            'activity' => '2000.00',
            'available' => '0.00',
        ]);

        $this->actingAs($user);

        Livewire::test('plan-sobres')
            ->call('edit', $sobre->id)
            ->set('gasto', '1000,00')
            ->set('gastoCuentaId', $cuenta->id)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('editingId', null);

        $mes = $sobre->meses()->first();

        $this->assertSame('3000.00', $mes->activity);
        $this->assertSame('2000.00', $mes->assigned);
        $this->assertSame('-1000.00', $mes->available);
        $this->assertSame('500.00', $cuenta->fresh()->balance);
        $this->assertSame(1, Gasto::query()->count());

        Livewire::test('plan-sobres')
            ->call('edit', $sobre->id)
            ->set('gasto', '-1,00')
            ->call('save')
            ->assertSee('El valor no es válido.');

        $this->assertSame('3000.00', $sobre->meses()->first()->activity);
    }

    public function test_sobre_amounts_accept_calculator_expressions_and_reject_a_negative_result(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->for($user)->create([
            'currency' => 'BOB',
            'number_format' => '1.234,56',
            'currency_placement' => 'antes',
        ]);
        $sobre = Sobre::factory()->for(Category::factory()->for($plan))->create(['name' => 'Comida']);
        $cuenta = Cuenta::factory()->for($plan)->create([
            'name' => 'Caja',
            'balance' => '50.00',
        ]);
        SobreMes::factory()->for($sobre)->create([
            'anio' => (int) now()->year,
            'mes' => (int) now()->month,
            'assigned' => '10.00',
            'activity' => '0.00',
            'available' => '10.00',
        ]);

        $this->actingAs($user);

        Livewire::test('plan-sobres')
            ->call('edit', $sobre->id)
            ->set('assigned', '10+5')
            ->set('available', '20-5')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('editingId', null);

        $mes = $sobre->meses()->first();

        $this->assertSame('15.00', $mes->assigned);
        $this->assertSame('15.00', $mes->available);

        Livewire::test('plan-sobres')
            ->call('edit', $sobre->id)
            ->set('gasto', '4*2')
            ->set('gastoCuentaId', $cuenta->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('8.00', $sobre->meses()->first()->activity);
        $this->assertSame('42.00', $cuenta->fresh()->balance);

        Livewire::test('plan-sobres')
            ->call('edit', $sobre->id)
            ->set('assigned', '10-20')
            ->call('save')
            ->assertSee('El valor no es válido.')
            ->assertSet('editingId', $sobre->id);

        $this->assertSame('15.00', $sobre->meses()->first()->assigned);
    }
}
