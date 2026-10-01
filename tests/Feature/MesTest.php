<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Plan;
use App\Models\Sobre;
use App\Models\SobreMes;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class MesTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_sobre_meses_amounts_are_numeric_and_migration_copies_nothing(): void
    {
        $this->assertTrue(Schema::hasColumns('sobre_meses', [
            'sobre_id',
            'anio',
            'mes',
            'assigned',
            'activity',
            'available',
        ]));

        $type = Schema::getColumnType('sobre_meses', 'available');

        $this->assertContains($type, ['decimal', 'numeric']);
        $this->assertNotContains($type, ['float', 'double', 'real']);

        $sobre = Sobre::factory()->create([
            'available' => '40.00',
        ]);

        $this->assertSame(0, SobreMes::query()->count());
        $this->assertSame('40.00', $sobre->available);

        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $dataType = DB::table('information_schema.columns')
            ->where('table_name', 'sobre_meses')
            ->where('column_name', 'available')
            ->value('data_type');

        $this->assertSame('numeric', $dataType);
    }

    public function test_arrows_change_one_month_and_a_guest_does_not(): void
    {
        $this->travelTo('2026-09-15');
        $user = User::factory()->create();
        Plan::factory()->for($user)->create();

        $this->actingAs($user);

        Livewire::test('plan-sobres')
            ->assertSet('year', 2026)
            ->assertSet('month', 9)
            ->assertSee('Septiembre 2026')
            ->call('nextMonth')
            ->assertSee('Octubre 2026')
            ->assertNoRedirect()
            ->call('previousMonth')
            ->call('previousMonth')
            ->assertSee('Agosto 2026')
            ->set('month', 12)
            ->call('nextMonth')
            ->assertSee('Enero 2027')
            ->set('month', 1)
            ->set('year', 2026)
            ->call('previousMonth')
            ->assertSee('Diciembre 2025');

        $html = $this->get('/')->assertOk()->assertSee('Dinero por asignar')->assertSee('Hoy')->getContent();

        $this->assertMatchesRegularExpression('/<button type="button" class="[^"]+">Hoy<\/button>/', $html);
        $this->assertDoesNotMatchRegularExpression('/wire:click="[^"]*">Hoy<\/button>/', $html);
    }

    public function test_guest_arrows_do_not_change_the_month(): void
    {
        Livewire::test('plan-sobres')
            ->call('nextMonth')
            ->call('previousMonth')
            ->assertSet('month', 0)
            ->assertSet('year', 0);
    }

    public function test_each_month_shows_its_own_amounts(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->for($user)->create([
            'currency' => 'BOB',
            'number_format' => '1.234,56',
            'currency_placement' => 'antes',
        ]);
        $sobre = Sobre::factory()->for(Category::factory()->for($plan))->create([
            'name' => 'Luz',
        ]);
        SobreMes::factory()->for($sobre)->create([
            'anio' => 2026,
            'mes' => 9,
            'assigned' => '280.00',
            'activity' => '40.00',
            'available' => '240.00',
        ]);
        SobreMes::factory()->for($sobre)->create([
            'anio' => 2026,
            'mes' => 10,
            'assigned' => '90.00',
            'activity' => '10.00',
            'available' => '80.00',
        ]);

        $this->actingAs($user);

        Livewire::test('plan-sobres')
            ->set('year', 2026)
            ->set('month', 9)
            ->assertSee('Luz')
            ->assertSee('Bs 280,00')
            ->assertSee('Bs 40,00')
            ->assertSee('Bs 240,00')
            ->assertDontSee('Bs 90,00')
            ->call('nextMonth')
            ->assertSee('Octubre 2026')
            ->assertSee('Bs 90,00')
            ->assertSee('Bs 10,00')
            ->assertSee('Bs 80,00')
            ->assertDontSee('Bs 280,00')
            ->assertDontSee('Bs 240,00')
            ->assertSee('Luz');
    }

    public function test_opening_the_next_month_does_not_copy_available_or_replace_the_dashboard(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->for($user)->create([
            'currency' => 'BOB',
            'number_format' => '1.234,56',
            'currency_placement' => 'antes',
        ]);
        $sobre = Sobre::factory()->for(Category::factory()->for($plan))->create([
            'name' => 'Luz',
            'available' => '40.00',
        ]);
        SobreMes::factory()->for($sobre)->create([
            'anio' => 2026,
            'mes' => 9,
            'assigned' => '50.00',
            'activity' => '10.00',
            'available' => '40.00',
        ]);

        $this->actingAs($user);

        Livewire::test('plan-sobres')
            ->set('year', 2026)
            ->set('month', 9)
            ->call('nextMonth')
            ->assertSee('Octubre 2026')
            ->assertSee('Bs 0,00')
            ->assertDontSee('Bs 40,00')
            ->assertSee('Luz')
            ->assertNoRedirect();

        $this->assertSame(1, SobreMes::query()->count());
        $this->assertSame('40.00', $sobre->meses()->first()->available);
        $this->assertNull($sobre->meses()->where('mes', 10)->first());

        $this->get('/')
            ->assertOk()
            ->assertSee('Crear plan')
            ->assertSee('Dinero por asignar');
    }

    public function test_several_plans_show_the_one_opened_this_visit(): void
    {
        $user = User::factory()->create();
        $older = Plan::factory()->for($user)->create();
        $latest = Plan::factory()->for($user)->create();
        Category::factory()->for($older)->create(['name' => 'Vieja']);
        Category::factory()->for($latest)->create(['name' => 'Nueva']);

        $this->actingAs($user);

        Livewire::test('plan-sobres')
            ->assertDontSee('Vieja')
            ->assertSee('Nueva')
            ->call('abrirPlan', $older->id)
            ->assertSee('Vieja')
            ->assertDontSee('Nueva');

        Livewire::test('plan-list')
            ->assertSet('openPlanId', $latest->id)
            ->call('open', $older->id)
            ->assertSet('openPlanId', $older->id);
    }
}
