<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Cuenta;
use App\Models\Ingreso;
use App\Models\Plan;
use App\Models\Sobre;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class IngresoTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_ingreso_amount_column_is_numeric_not_float(): void
    {
        $this->assertTrue(Schema::hasColumns('ingresos', [
            'cuenta_id',
            'fecha',
            'origen',
            'descripcion',
            'monto',
        ]));
        $this->assertFalse(Schema::hasTable('gastos'));

        $type = Schema::getColumnType('ingresos', 'monto');

        $this->assertContains($type, ['decimal', 'numeric']);
        $this->assertNotContains($type, ['float', 'double', 'real']);

        $ingreso = Ingreso::factory()->create([
            'monto' => '10.50',
        ]);

        $this->assertIsString($ingreso->monto);
        $this->assertSame('10.50', $ingreso->monto);

        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $dataType = DB::table('information_schema.columns')
            ->where('table_name', 'ingresos')
            ->where('column_name', 'monto')
            ->value('data_type');

        $this->assertSame('numeric', $dataType);
    }

    public function test_income_form_shows_four_fields_and_saves_today(): void
    {
        $this->travelTo('2026-04-03');
        $user = User::factory()->create();
        $cuenta = $this->cuentaFor($user);

        $this->actingAs($user);

        Livewire::test('pages::cuenta', ['cuenta' => $cuenta])
            ->assertSet('fecha', '2026-04-03')
            ->assertSee('Fecha')
            ->assertSee('De quién o cómo entra el dinero')
            ->assertSee('Descripción')
            ->assertSee('Monto')
            ->set('origen', 'Sueldo')
            ->set('descripcion', 'Pago de abril')
            ->set('monto', '2,50')
            ->call('save')
            ->assertNoRedirect();

        $ingreso = Ingreso::query()->first();

        $this->assertNotNull($ingreso);
        $this->assertSame($cuenta->id, $ingreso->cuenta_id);
        $this->assertSame('2026-04-03', $ingreso->fecha->toDateString());
        $this->assertSame('Sueldo', $ingreso->origen);
        $this->assertSame('Pago de abril', $ingreso->descripcion);
        $this->assertIsString($ingreso->monto);
        $this->assertSame('2.50', $ingreso->monto);
    }

    public function test_a_changed_date_is_stored(): void
    {
        $user = User::factory()->create();
        $cuenta = $this->cuentaFor($user);

        $this->actingAs($user);

        Livewire::test('pages::cuenta', ['cuenta' => $cuenta])
            ->set('fecha', '2026-05-01')
            ->set('origen', 'Sueldo')
            ->set('descripcion', 'Pago')
            ->set('monto', '2,50')
            ->call('save');

        $this->assertSame('2026-05-01', Ingreso::query()->first()->fecha->toDateString());
    }

    public function test_income_is_not_created_on_another_users_account(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $foreign = $this->cuentaFor($other, '8.00');

        $this->actingAs($user)
            ->get(route('cuentas.show', $foreign))
            ->assertNotFound();

        $this->assertSame(0, Ingreso::query()->count());
        $this->assertSame('8.00', $foreign->fresh()->balance);
    }

    public function test_guest_cannot_open_the_account_income_form(): void
    {
        $user = User::factory()->create();
        $cuenta = $this->cuentaFor($user);

        $this->get(route('cuentas.show', $cuenta))->assertRedirect(route('login'));

        $this->assertSame(0, Ingreso::query()->count());
    }

    public function test_empty_field_shows_the_error_and_does_not_change_the_balance(): void
    {
        $user = User::factory()->create();
        $cuenta = $this->cuentaFor($user, '10.00');

        $this->actingAs($user);

        Livewire::test('pages::cuenta', ['cuenta' => $cuenta])
            ->set('origen', 'Sueldo')
            ->set('descripcion', '')
            ->set('monto', '2,50')
            ->call('save')
            ->assertHasErrors(['descripcion' => 'required'])
            ->assertSee('Este campo es obligatorio.')
            ->assertDontSee('ingreso registrado exitosamente');

        $this->assertSame(0, Ingreso::query()->count());
        $this->assertSame('10.00', $cuenta->fresh()->balance);
    }

    public function test_non_numeric_amount_shows_the_error_and_does_not_change_the_balance(): void
    {
        $user = User::factory()->create();
        $cuenta = $this->cuentaFor($user, '10.00');

        $this->actingAs($user);

        Livewire::test('pages::cuenta', ['cuenta' => $cuenta])
            ->set('origen', 'Sueldo')
            ->set('descripcion', 'Pago')
            ->set('monto', 'abc')
            ->call('save')
            ->assertSee('El valor no es válido.')
            ->assertDontSee('ingreso registrado exitosamente');

        $this->assertSame(0, Ingreso::query()->count());
        $this->assertSame('10.00', $cuenta->fresh()->balance);

        Livewire::test('pages::cuenta', ['cuenta' => $cuenta])
            ->set('origen', 'Sueldo')
            ->set('descripcion', 'Pago')
            ->set('monto', '-2,50')
            ->call('save')
            ->assertSee('El valor no es válido.');

        $this->assertSame('10.00', $cuenta->fresh()->balance);
    }

    public function test_valid_income_shows_confirmation_and_adds_the_amount(): void
    {
        $user = User::factory()->create();
        $cuenta = $this->cuentaFor($user, '10.00');
        $sobre = Sobre::factory()->for(Category::factory()->for($cuenta->plan))->create([
            'assigned' => '5.00',
            'activity' => '1.00',
            'available' => '4.00',
        ]);

        $this->actingAs($user);

        Livewire::test('pages::cuenta', ['cuenta' => $cuenta])
            ->set('origen', 'Sueldo')
            ->set('descripcion', 'Pago')
            ->set('monto', '2,50')
            ->call('save')
            ->assertSee('ingreso registrado exitosamente')
            ->assertSee('Bs 12,50')
            ->assertNoRedirect()
            ->assertDontSee('Sueldo');

        $ingreso = Ingreso::query()->first();

        $this->assertNotNull($ingreso);
        $this->assertIsString($ingreso->monto);
        $this->assertSame('2.50', $ingreso->monto);
        $this->assertSame('12.50', $cuenta->fresh()->balance);
        $this->assertIsString($cuenta->fresh()->balance);
        $this->assertSame('5.00', $sobre->fresh()->assigned);
        $this->assertSame('1.00', $sobre->fresh()->activity);
        $this->assertSame('4.00', $sobre->fresh()->available);

        $this->get('/')
            ->assertSee('Dinero por asignar')
            ->assertSee('Bs 1.250,00');
    }

    public function test_account_view_does_not_save_an_expense(): void
    {
        $user = User::factory()->create();
        $cuenta = $this->cuentaFor($user, '10.00');

        $this->actingAs($user)
            ->get(route('cuentas.show', $cuenta))
            ->assertOk()
            ->assertDontSee('gasto')
            ->assertDontSee('Agregar gasto');

        $this->assertFalse(Schema::hasTable('gastos'));
        $this->assertSame(0, Ingreso::query()->count());
        $this->assertSame('10.00', $cuenta->fresh()->balance);
    }

    private function cuentaFor(User $user, string $balance = '10.00'): Cuenta
    {
        $plan = Plan::factory()->for($user)->create([
            'currency' => 'BOB',
            'number_format' => '1.234,56',
            'currency_placement' => 'antes',
        ]);

        return Cuenta::factory()->for($plan)->create([
            'name' => 'Efectivo',
            'balance' => $balance,
        ]);
    }
}
