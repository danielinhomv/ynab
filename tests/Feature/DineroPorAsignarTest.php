<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Cuenta;
use App\Models\Ingreso;
use App\Models\Plan;
use App\Models\Sobre;
use App\Models\SobreMes;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DineroPorAsignarTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo('2026-09-15 10:00:00');
    }

    public function test_card_shows_the_visible_month_total_in_the_plan_format(): void
    {
        $user = User::factory()->create();
        $plan = $this->planFor($user, ['currency' => 'USD', 'number_format' => '1,234.56', 'currency_placement' => 'después']);
        $sobre = $this->sobreFor($plan);
        $cuenta = Cuenta::factory()->for($plan)->create(['balance' => '0.00']);
        $this->income($cuenta, '2026-09-03', '2000.00');
        $this->income($cuenta, '2026-09-20', '250.75');
        $this->budget($sobre, 9, '1000.25');

        $foreign = $this->planFor(User::factory()->create());
        $this->income(Cuenta::factory()->for($foreign)->create(), '2026-09-01', '9999.00');
        $this->budget($this->sobreFor($foreign), 9, '1.00');

        $this->actingAs($user);

        $component = Livewire::test('plan-sobres')
            ->assertSeeHtml('tracking-tight text-slate-900">1,250.50 USD')
            ->assertDontSee('Resolver ahora');

        $this->assertSame('1250.50', $component->instance()->moneyToAssign);

        $this->get('/')
            ->assertOk()
            ->assertSee('Dinero por asignar')
            ->assertSee('1,250.50 USD')
            ->assertDontSee('Bs 1.250,00')
            ->assertDontSee('Bs 9.800,00')
            ->assertDontSee('Bs 8.550,00')
            ->assertDontSee('Bs 4.120,50')
            ->assertSee('2,250.75 USD')
            ->assertSee('1,000.25 USD')
            ->assertSee('0.00 USD')
            ->assertSee('2 depósitos recibidos · 1 sobre activo')
            ->assertSee('0,0% del presupuesto')
            ->assertSee('Auto-asignar');
    }

    public function test_deficits_carry_into_later_months_and_accumulate_without_changing_sobres(): void
    {
        $user = User::factory()->create();
        $plan = $this->planFor($user);
        $sobre = $this->sobreFor($plan);
        $cuenta = Cuenta::factory()->for($plan)->create();

        $this->income($cuenta, '2026-06-10', '5000.00');
        $this->income($cuenta, '2026-07-10', '1000.00');
        $this->budget($sobre, 7, '1200.00');
        $this->income($cuenta, '2026-08-10', '500.00');
        $this->budget($sobre, 8, '600.00');
        $this->income($cuenta, '2026-09-10', '1000.00');
        $this->budget($sobre, 9, '800.00');

        $this->actingAs($user);

        $component = Livewire::test('plan-sobres')
            ->assertSeeHtml('tracking-tight text-red-600">-Bs 100,00');
        $this->assertSame('-100.00', $component->instance()->moneyToAssign);

        $component->call('previousMonth')
            ->assertSeeHtml('tracking-tight text-red-600">-Bs 300,00');

        $component->call('previousMonth')
            ->assertSeeHtml('tracking-tight text-red-600">-Bs 200,00');

        $component->call('previousMonth')
            ->assertSeeHtml('tracking-tight text-slate-900">Bs 5.000,00');

        $component->call('nextMonth')->call('nextMonth')->call('nextMonth')->call('nextMonth')
            ->assertSee('Octubre 2026')
            ->assertSeeHtml('tracking-tight text-red-600">-Bs 300,00');

        $sobre->refresh();
        $this->assertSame('0.00', $sobre->assigned);
        $this->assertSame('0.00', $sobre->activity);
        $this->assertSame('0.00', $sobre->available);
        $this->assertSame(3, $sobre->meses()->count());
        $this->assertSame('800.00', $sobre->meses()->where('mes', 9)->first()->assigned);
        $this->assertSame('0.00', $sobre->meses()->where('mes', 9)->first()->available);
        $this->assertSame(0, $sobre->meses()->where('mes', 10)->count());
    }

    public function test_no_total_is_shown_until_a_plan_is_open(): void
    {
        $user = User::factory()->create();
        $older = $this->planFor($user);
        $this->planFor($user);
        $this->income(Cuenta::factory()->for($older)->create(), '2026-09-01', '75.00');

        $this->actingAs($user);

        Livewire::test('plan-sobres')
            ->assertDontSee('Bs 75,00')
            ->assertDontSee('Bs 1.250,00')
            ->assertSeeHtml('tracking-tight text-slate-900">Bs 0,00')
            ->call('abrirPlan', $older->id)
            ->assertSeeHtml('tracking-tight text-slate-900">Bs 75,00');
    }

    public function test_summary_cards_use_income_assigned_and_activity_of_the_visible_month(): void
    {
        $user = User::factory()->create();
        $plan = $this->planFor($user);
        $sobre = $this->sobreFor($plan);
        $cuenta = Cuenta::factory()->for($plan)->create();
        $this->income($cuenta, '2026-09-01', '5000.00');
        $this->income($cuenta, '2026-08-01', '9000.00');
        SobreMes::factory()->for($sobre)->create([
            'anio' => 2026,
            'mes' => 9,
            'assigned' => '2000.00',
            'activity' => '500.00',
            'available' => '1500.00',
        ]);

        $this->actingAs($user);

        $totals = Livewire::test('plan-sobres')->instance()->monthTotals;

        $this->assertSame('5000.00', $totals['income']);
        $this->assertSame('2000.00', $totals['budgeted']);
        $this->assertSame('500.00', $totals['spent']);
        $this->assertSame(1, $totals['incomeCount']);
        $this->assertSame(1, $totals['sobreCount']);

        Livewire::test('plan-sobres')
            ->assertSee('Bs 5.000,00')
            ->assertSee('Bs 2.000,00')
            ->assertSee('Bs 500,00')
            ->assertSee('1 depósito recibido · 1 sobre activo')
            ->assertSee('25,0% del presupuesto')
            ->assertDontSee('Bs 9.000,00')
            ->call('previousMonth')
            ->assertSee('Bs 9.000,00')
            ->assertSee('Bs 0,00')
            ->assertSee('0,0% del presupuesto');
    }

    /**
     * @param  array<string, string>  $attributes
     */
    private function planFor(User $user, array $attributes = []): Plan
    {
        return Plan::factory()->for($user)->create([
            'currency' => 'BOB',
            'number_format' => '1.234,56',
            'currency_placement' => 'antes',
            ...$attributes,
        ]);
    }

    private function sobreFor(Plan $plan): Sobre
    {
        return Sobre::factory()->for(Category::factory()->for($plan))->create();
    }

    private function income(Cuenta $cuenta, string $fecha, string $monto): void
    {
        Ingreso::factory()->for($cuenta)->create(['fecha' => $fecha, 'monto' => $monto]);
    }

    private function budget(Sobre $sobre, int $month, string $assigned): void
    {
        SobreMes::factory()->for($sobre)->create([
            'anio' => 2026,
            'mes' => $month,
            'assigned' => $assigned,
        ]);
    }
}
