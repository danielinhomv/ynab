<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Plan;
use App\Models\Sobre;
use App\Models\SobreMes;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SobregastoTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_overspent_row_shows_sobregiro_and_a_covered_row_does_not(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->for($user)->create([
            'currency' => 'BOB',
            'number_format' => '1.234,56',
            'currency_placement' => 'antes',
        ]);
        $category = Category::factory()->for($plan)->create(['name' => 'Casa']);
        $problem = Sobre::factory()->for($category)->create([
            'name' => 'Mercado',
            'assigned' => '1.00',
            'activity' => '1.00',
            'available' => '1.00',
        ]);
        $covered = Sobre::factory()->for($category)->create([
            'name' => 'Ahorro',
            'assigned' => '1.00',
            'activity' => '1.00',
            'available' => '-10.00',
        ]);
        SobreMes::factory()->for($problem)->create([
            'anio' => (int) now()->year,
            'mes' => (int) now()->month,
            'assigned' => '100.50',
            'activity' => '151.00',
            'available' => '-50.50',
        ]);
        SobreMes::factory()->for($covered)->create([
            'anio' => (int) now()->year,
            'mes' => (int) now()->month,
            'assigned' => '80.00',
            'activity' => '40.00',
            'available' => '-10.00',
        ]);

        $this->actingAs($user);

        Livewire::test('plan-sobres')
            ->assertSee('Mercado')
            ->assertSee('Sobregiro')
            ->assertSee('Bs 100,50')
            ->assertSee('Bs 151,00')
            ->assertSeeHtml('bg-red-50/70')
            ->assertSeeHtml('text-red-600')
            ->assertDontSeeHtml('openCover('.$problem->id.')')
            ->assertDontSee('Resolver ahora')
            ->assertDontSee('Cubrir sobregiro')
            ->call('edit', $problem->id)
            ->assertSet('editingId', $problem->id)
            ->assertSet('coverSobreId', null);

        $html = Livewire::test('plan-sobres')->html();
        $coveredRow = strstr($html, 'Ahorro');
        $this->assertNotFalse($coveredRow);
        $this->assertStringNotContainsString('Sobregiro', substr($coveredRow, 0, 400));

        $this->assertIsString($problem->meses()->first()->assigned);
        $this->assertSame('100.50', $problem->meses()->first()->assigned);
        $this->assertSame('1.00', $problem->fresh()->assigned);
    }

    public function test_cubrir_opens_the_card_on_the_same_view(): void
    {
        [$user, $problem, $source, $foreign] = $this->overspentPair();

        $this->actingAs($user);

        Livewire::test('plan-sobres')
            ->call('openCover', $problem->id)
            ->assertNoRedirect()
            ->assertSee('Cubrir sobregiro')
            ->assertSee('Mercado')
            ->assertSee('Ahorro')
            ->assertSee('Bs 50,50')
            ->assertSeeHtml('border-red-100')
            ->assertSeeHtml('bg-forest')
            ->assertSeeHtml('coverTakes.')
            ->assertDontSee('Ajeno')
            ->assertDontSee('Dinero por asignar')
            ->assertDontSee('Resolver ahora')
            ->call('closeCover')
            ->assertDontSee('Cubrir sobregiro');

        $this->assertSame('100.00', $problem->meses()->first()->assigned);
        $this->assertSame('50.50', $source->meses()->first()->assigned);
    }

    public function test_confirm_moves_the_full_shortfall_on_the_visible_month(): void
    {
        [$user, $problem, $source] = $this->overspentPair();
        $otherMonth = SobreMes::factory()->for($problem)->create([
            'anio' => 2020,
            'mes' => 1,
            'assigned' => '10.00',
            'activity' => '4.00',
            'available' => '6.00',
        ]);

        $this->actingAs($user);

        Livewire::test('plan-sobres')
            ->call('openCover', $problem->id)
            ->call('confirmCover')
            ->assertNoRedirect()
            ->assertSee('sobre cubierto exitosamente');

        $problemMonth = $problem->meses()->where('anio', now()->year)->where('mes', now()->month)->first();
        $sourceMonth = $source->meses()->where('anio', now()->year)->where('mes', now()->month)->first();

        $this->assertSame('150.50', $problemMonth->assigned);
        $this->assertSame('0.00', $sourceMonth->assigned);
        $this->assertSame('150.50', $problemMonth->activity);
        $this->assertSame('0.00', $sourceMonth->activity);
        $this->assertSame('0.00', $problemMonth->available);
        $this->assertSame('0.00', $sourceMonth->available);
        $this->assertIsString($problemMonth->assigned);
        $this->assertIsString($sourceMonth->assigned);
        $this->assertSame('10.00', $otherMonth->fresh()->assigned);
        $this->assertSame('4.00', $otherMonth->fresh()->activity);
        $this->assertSame('1.00', $problem->fresh()->assigned);
        $this->assertSame('1.00', $source->fresh()->activity);
    }

    public function test_covered_row_loses_red_and_blocked_moves_save_nothing(): void
    {
        [$user, $problem, $source] = $this->overspentPair();

        $this->actingAs($user);

        Livewire::test('plan-sobres')
            ->call('openCover', $problem->id)
            ->call('confirmCover')
            ->assertSee('sobre cubierto exitosamente')
            ->assertDontSee('Sobregiro')
            ->assertDontSeeHtml('bg-red-50/70')
            ->assertDontSeeHtml('text-right text-sm font-medium text-red-600');

        $this->get('/')
            ->assertOk()
            ->assertSee('Dinero por asignar')
            ->assertSee('-Bs 150,50')
            ->assertDontSee('Bs 1.250,00');

        $alone = $this->aloneOverspent();

        Livewire::actingAs($alone['user'])->test('plan-sobres')
            ->assertSee('Sobregiro')
            ->assertDontSeeHtml('openCover('.$alone['sobre']->id.')')
            ->call('openCover', $alone['sobre']->id)
            ->assertDontSee('Cubrir sobregiro');

        $this->assertSame('20.00', $alone['sobre']->meses()->first()->assigned);

        $short = $this->shortSource();

        Livewire::actingAs($short['user'])->test('plan-sobres')
            ->assertSee('Sobregiro')
            ->assertDontSeeHtml('openCover('.$short['problem']->id.')')
            ->call('openCover', $short['problem']->id)
            ->assertDontSee('Cubrir sobregiro');

        $this->assertSame('100.00', $short['problem']->meses()->first()->assigned);
        $this->assertSame('10.00', $short['source']->meses()->first()->assigned);
        $this->assertSame('-40.00', $short['problem']->meses()->first()->available);
        $this->assertSame(0, $short['empty']->meses()->count());
    }

    public function test_cover_splits_across_sobres_and_rejects_an_over_total(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->for($user)->create([
            'currency' => 'BOB',
            'number_format' => '1.234,56',
            'currency_placement' => 'antes',
        ]);
        $category = Category::factory()->for($plan)->create();
        $problem = Sobre::factory()->for($category)->create(['name' => 'Mercado']);
        $one = Sobre::factory()->for($category)->create(['name' => 'Uno']);
        $two = Sobre::factory()->for($category)->create(['name' => 'Dos']);
        SobreMes::factory()->for($problem)->create([
            'anio' => (int) now()->year,
            'mes' => (int) now()->month,
            'assigned' => '100.00',
            'activity' => '160.00',
            'available' => '0.00',
        ]);
        SobreMes::factory()->for($one)->create([
            'anio' => (int) now()->year,
            'mes' => (int) now()->month,
            'assigned' => '30.00',
        ]);
        SobreMes::factory()->for($two)->create([
            'anio' => (int) now()->year,
            'mes' => (int) now()->month,
            'assigned' => '40.00',
        ]);

        $this->actingAs($user);

        $component = Livewire::test('plan-sobres')
            ->assertSeeHtml('openCover('.$problem->id.')')
            ->call('openCover', $problem->id)
            ->assertSee('Falta:')
            ->assertSee('Cubierto:')
            ->assertSee('Dejas este sobre en 0.')
            ->set('coverTakes.'.$one->id, '40,00')
            ->assertSee('El valor no es válido.')
            ->set('coverTakes.'.$one->id, '30,00')
            ->set('coverTakes.'.$two->id, '40,00')
            ->assertSee('Te estás pasando del monto a cubrir.')
            ->call('confirmCover')
            ->assertSee('Te estás pasando del monto a cubrir.');

        $this->assertSame('100.00', $problem->meses()->first()->assigned);

        $component
            ->set('coverTakes.'.$two->id, '30,00')
            ->call('confirmCover')
            ->assertSee('sobre cubierto exitosamente');

        $this->assertSame('160.00', $problem->meses()->first()->assigned);
        $this->assertSame('0.00', $one->meses()->first()->assigned);
        $this->assertSame('10.00', $two->meses()->first()->assigned);
        $this->assertSame('0.00', $problem->meses()->first()->available);
        $this->assertSame('0.00', $one->meses()->first()->available);
        $this->assertSame('10.00', $two->meses()->first()->available);
    }

    public function test_cover_uses_unspent_available_and_hides_a_spent_source(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->for($user)->create([
            'currency' => 'BOB',
            'number_format' => '1.234,56',
            'currency_placement' => 'antes',
        ]);
        $category = Category::factory()->for($plan)->create(['name' => 'educacion']);
        $problem = Sobre::factory()->for($category)->create(['name' => 'material escolar']);
        $spent = Sobre::factory()->for($category)->create(['name' => 'colegio']);
        $open = Sobre::factory()->for($category)->create(['name' => 'universidad']);
        SobreMes::factory()->for($problem)->create([
            'anio' => (int) now()->year,
            'mes' => (int) now()->month,
            'assigned' => '500.00',
            'activity' => '600.00',
            'available' => '-100.00',
        ]);
        SobreMes::factory()->for($spent)->create([
            'anio' => (int) now()->year,
            'mes' => (int) now()->month,
            'assigned' => '1000.00',
            'activity' => '1000.00',
            'available' => '0.00',
        ]);
        SobreMes::factory()->for($open)->create([
            'anio' => (int) now()->year,
            'mes' => (int) now()->month,
            'assigned' => '2000.00',
            'activity' => '100.00',
            'available' => '1900.00',
        ]);

        $this->actingAs($user);

        Livewire::test('plan-sobres')
            ->call('openCover', $problem->id)
            ->assertSee('Cubrir sobregiro')
            ->assertSee('tiene Bs 1.900,00')
            ->assertDontSee('tiene Bs 1.000,00')
            ->assertDontSee('tiene Bs 2.000,00')
            ->call('confirmCover')
            ->assertSee('sobre cubierto exitosamente');

        $this->assertSame('600.00', $problem->meses()->first()->assigned);
        $this->assertSame('0.00', $problem->meses()->first()->available);
        $this->assertSame('1000.00', $spent->meses()->first()->assigned);
        $this->assertSame('1900.00', $open->meses()->first()->assigned);
        $this->assertSame('1800.00', $open->meses()->first()->available);
    }

    /**
     * @return array{0: User, 1: Sobre, 2: Sobre, 3: Sobre}
     */
    private function overspentPair(): array
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $plan = Plan::factory()->for($user)->create([
            'currency' => 'BOB',
            'number_format' => '1.234,56',
            'currency_placement' => 'antes',
        ]);
        $category = Category::factory()->for($plan)->create(['name' => 'Casa']);
        $problem = Sobre::factory()->for($category)->create([
            'name' => 'Mercado',
            'assigned' => '1.00',
            'activity' => '1.00',
            'available' => '1.00',
        ]);
        $source = Sobre::factory()->for($category)->create([
            'name' => 'Ahorro',
            'assigned' => '1.00',
            'activity' => '1.00',
            'available' => '1.00',
        ]);
        SobreMes::factory()->for($problem)->create([
            'anio' => (int) now()->year,
            'mes' => (int) now()->month,
            'assigned' => '100.00',
            'activity' => '150.50',
            'available' => '-50.50',
        ]);
        SobreMes::factory()->for($source)->create([
            'anio' => (int) now()->year,
            'mes' => (int) now()->month,
            'assigned' => '50.50',
            'activity' => '0.00',
            'available' => '40.00',
        ]);
        $foreign = Sobre::factory()->for(
            Category::factory()->for(Plan::factory()->for($other))
        )->create(['name' => 'Ajeno']);
        SobreMes::factory()->for($foreign)->create([
            'anio' => (int) now()->year,
            'mes' => (int) now()->month,
            'assigned' => '500.00',
            'activity' => '0.00',
            'available' => '500.00',
        ]);

        return [$user, $problem, $source, $foreign];
    }

    /**
     * @return array{user: User, sobre: Sobre}
     */
    private function aloneOverspent(): array
    {
        $user = User::factory()->create();
        $sobre = Sobre::factory()->for(
            Category::factory()->for(Plan::factory()->for($user))
        )->create(['name' => 'Unico']);
        SobreMes::factory()->for($sobre)->create([
            'anio' => (int) now()->year,
            'mes' => (int) now()->month,
            'assigned' => '20.00',
            'activity' => '35.00',
            'available' => '-15.00',
        ]);

        return ['user' => $user, 'sobre' => $sobre];
    }

    /**
     * @return array{user: User, problem: Sobre, source: Sobre, empty: Sobre}
     */
    private function shortSource(): array
    {
        $user = User::factory()->create();
        $category = Category::factory()->for(Plan::factory()->for($user))->create();
        $problem = Sobre::factory()->for($category)->create(['name' => 'Mercado']);
        $source = Sobre::factory()->for($category)->create(['name' => 'Corto']);
        $empty = Sobre::factory()->for($category)->create(['name' => 'Vacio']);
        SobreMes::factory()->for($problem)->create([
            'anio' => (int) now()->year,
            'mes' => (int) now()->month,
            'assigned' => '100.00',
            'activity' => '140.00',
            'available' => '-40.00',
        ]);
        SobreMes::factory()->for($source)->create([
            'anio' => (int) now()->year,
            'mes' => (int) now()->month,
            'assigned' => '10.00',
            'activity' => '0.00',
            'available' => '10.00',
        ]);

        return ['user' => $user, 'problem' => $problem, 'source' => $source, 'empty' => $empty];
    }
}
