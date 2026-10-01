<?php

namespace Tests\Feature;

use App\Models\Cuenta;
use App\Models\Ingreso;
use App\Models\Plan;
use App\Models\Transferencia;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class TransferenciaTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_transfer_converts_between_plans_and_dates_the_destination_income(): void
    {
        $this->travelTo('2026-09-30');
        Http::preventStrayRequests();
        Http::fake([
            'https://open.er-api.com/v6/latest/USD' => Http::response([
                'result' => 'success',
                'rates' => [
                    'USD' => 1,
                    'EUR' => 0.9,
                    'BOB' => 6.96,
                ],
            ]),
        ]);
        Cache::forget('exchange-usd-rates');

        $user = User::factory()->create();
        $usdPlan = Plan::factory()->for($user)->create([
            'name' => 'Dolares',
            'currency' => 'USD',
            'number_format' => '1,234.56',
            'currency_placement' => 'después',
        ]);
        $bobPlan = Plan::factory()->for($user)->create([
            'name' => 'Bolivianos',
            'currency' => 'BOB',
            'number_format' => '1.234,56',
            'currency_placement' => 'antes',
        ]);
        $from = Cuenta::factory()->for($usdPlan)->create([
            'name' => 'Banco USD',
            'balance' => '20.00',
        ]);
        $to = Cuenta::factory()->for($bobPlan)->create([
            'name' => 'Caja BOB',
            'balance' => '1.00',
        ]);

        $this->actingAs($user);

        Livewire::test('plan-accounts')
            ->call('abrirPlan', $bobPlan->id)
            ->call('openTransfer')
            ->set('fromCuentaId', $from->id)
            ->set('toCuentaId', $to->id)
            ->set('transferAmount', '10.00')
            ->set('transferMonth', 8)
            ->set('transferYear', 2026)
            ->call('saveTransfer')
            ->assertHasNoErrors()
            ->assertSet('transferOpen', false)
            ->assertSee('transferencia registrada exitosamente');

        $this->assertSame('10.00', $from->fresh()->balance);
        $this->assertSame('70.60', $to->fresh()->balance);

        $ingreso = Ingreso::query()->first();
        $this->assertNotNull($ingreso);
        $this->assertSame($to->id, $ingreso->cuenta_id);
        $this->assertSame('2026-08-01', $ingreso->fecha->toDateString());
        $this->assertSame('Transferencia', $ingreso->origen);
        $this->assertSame('69.60', $ingreso->monto);

        $transfer = Transferencia::query()->first();
        $this->assertNotNull($transfer);
        $this->assertSame('10.00', $transfer->monto_origen);
        $this->assertSame('69.60', $transfer->monto_destino);
        $this->assertSame('USD', $transfer->moneda_origen);
        $this->assertSame('BOB', $transfer->moneda_destino);
        $this->assertSame(8, $transfer->mes);
    }

    public function test_transfer_fails_when_the_rate_api_fails(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://open.er-api.com/v6/latest/USD' => Http::response(['result' => 'error'], 500),
        ]);
        Cache::forget('exchange-usd-rates');

        $user = User::factory()->create();
        $usdPlan = Plan::factory()->for($user)->create(['currency' => 'USD']);
        $bobPlan = Plan::factory()->for($user)->create(['currency' => 'BOB']);
        $from = Cuenta::factory()->for($usdPlan)->create(['balance' => '20.00']);
        $to = Cuenta::factory()->for($bobPlan)->create(['balance' => '1.00']);

        $this->actingAs($user);

        Livewire::test('plan-accounts')
            ->call('openTransfer')
            ->set('fromCuentaId', $from->id)
            ->set('toCuentaId', $to->id)
            ->set('transferAmount', '5.00')
            ->set('transferMonth', (int) now()->month)
            ->set('transferYear', (int) now()->year)
            ->call('saveTransfer')
            ->assertHasErrors(['transferAmount']);

        $this->assertSame('20.00', $from->fresh()->balance);
        $this->assertSame(0, Transferencia::query()->count());
        $this->assertSame(0, Ingreso::query()->count());
    }

    public function test_transfer_rejects_foreign_accounts_and_insufficient_balance(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://open.er-api.com/v6/latest/USD' => Http::response([
                'result' => 'success',
                'rates' => [
                    'USD' => 1,
                    'EUR' => 0.9,
                    'BOB' => 6.96,
                ],
            ]),
        ]);
        Cache::forget('exchange-usd-rates');

        $user = User::factory()->create();
        $other = User::factory()->create();
        $plan = Plan::factory()->for($user)->create(['currency' => 'BOB']);
        $from = Cuenta::factory()->for($plan)->create(['balance' => '2.00']);
        $to = Cuenta::factory()->for($plan)->create(['balance' => '0.00']);
        $foreign = Cuenta::factory()->for(Plan::factory()->for($other))->create(['balance' => '50.00']);

        $this->actingAs($user);

        Livewire::test('plan-accounts')
            ->set('fromCuentaId', $from->id)
            ->set('toCuentaId', $foreign->id)
            ->set('transferAmount', '1.00')
            ->set('transferMonth', (int) now()->month)
            ->set('transferYear', (int) now()->year)
            ->call('saveTransfer');

        $this->assertSame('2.00', $from->fresh()->balance);
        $this->assertSame('50.00', $foreign->fresh()->balance);

        Livewire::test('plan-accounts')
            ->call('openTransfer')
            ->set('fromCuentaId', $from->id)
            ->set('toCuentaId', $to->id)
            ->set('transferAmount', '5.00')
            ->set('transferMonth', (int) now()->month)
            ->set('transferYear', (int) now()->year)
            ->call('saveTransfer')
            ->assertHasErrors(['transferAmount']);

        $this->assertSame('2.00', $from->fresh()->balance);
        $this->assertSame(0, Transferencia::query()->count());
    }
}
