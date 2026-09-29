<?php

namespace Tests\Feature;

use App\Models\Cuenta;
use App\Models\Plan;
use App\Models\User;
use DOMDocument;
use DOMElement;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class CuentaListTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_add_account_popup_offers_two_types_and_hides_from_guests(): void
    {
        $user = User::factory()->create();

        Plan::factory()->for($user)->create();

        $this->actingAs($user);

        Livewire::test('plan-accounts')
            ->call('openPopup')
            ->assertSee('Tarjeta de crédito')
            ->assertSee('Cuenta indefinida (efectivo)')
            ->assertDontSee('Conectar un banco');
    }

    public function test_guest_cannot_open_the_add_account_popup(): void
    {
        Livewire::test('plan-accounts')
            ->call('openPopup')
            ->assertDontSee('Tarjeta de crédito')
            ->assertDontSee('Cuenta indefinida (efectivo)');

        $this->get('/')->assertDontSee('Tarjeta de crédito');
    }

    public function test_accounts_and_total_come_from_the_database(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->for($user)->create([
            'currency' => 'BOB',
            'number_format' => '1.234,56',
            'currency_placement' => 'antes',
        ]);
        $cuenta = Cuenta::factory()->for($plan)->create([
            'type' => 'tarjeta_credito',
            'name' => 'Visa hogar',
            'balance' => '1250.00',
        ]);
        Cuenta::factory()->for($plan)->create([
            'type' => 'indefinida',
            'name' => 'Caja',
            'balance' => '100.50',
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertOk();
        $response->assertSee('Visa hogar');
        $response->assertSee('Caja');
        $response->assertSee('Bs 1.250,00');
        $response->assertSee('Bs 100,50');
        $response->assertSee('Bs 1.350,50');
        $response->assertSee('Editar');
        $response->assertDontSee('Banco Sol');
        $response->assertDontSee('Banco Ganadero');
        $response->assertDontSee('Bs 14.850,00');
        $response->assertSee(route('cuentas.show', $cuenta), false);

        $document = new DOMDocument;
        $loaded = $document->loadHTML($response->getContent(), LIBXML_NOERROR);

        $this->assertTrue($loaded);

        $aside = $document->getElementsByTagName('aside')->item(0);

        $this->assertInstanceOf(DOMElement::class, $aside);
        $this->assertStringContainsString('Visa hogar', $aside->textContent);
        $this->assertStringContainsString('Bs 1.350,50', $aside->textContent);
        $this->assertStringNotContainsString('Banco Sol', $aside->textContent);

        $editButtons = [];

        foreach ($aside->getElementsByTagName('button') as $button) {
            if ($button->getAttribute('aria-label') === 'Editar') {
                $editButtons[] = $button;
            }
        }

        $this->assertCount(2, $editButtons);
        $this->assertSame('', $editButtons[0]->getAttribute('href'));
    }

    public function test_clicking_the_account_opens_its_view_and_a_foreign_account_does_not(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $plan = Plan::factory()->for($user)->create();
        $cuenta = Cuenta::factory()->for($plan)->create([
            'name' => 'Efectivo de casa',
        ]);
        $foreign = Cuenta::factory()->for(Plan::factory()->for($other))->create([
            'name' => 'Cuenta ajena',
        ]);

        $this->actingAs($user)
            ->get(route('cuentas.show', $cuenta))
            ->assertOk()
            ->assertSee('Efectivo de casa')
            ->assertDontSee('Cuenta ajena');

        $this->actingAs($user)
            ->get(route('cuentas.show', $foreign))
            ->assertNotFound();
    }

    public function test_valid_accounts_are_stored_on_the_latest_plan_as_decimals(): void
    {
        $user = User::factory()->create();
        $older = Plan::factory()->for($user)->create();
        $latest = Plan::factory()->for($user)->create();

        $this->actingAs($user);

        Livewire::test('plan-accounts')
            ->call('openPopup')
            ->call('chooseType', 'tarjeta_credito')
            ->set('name', 'Visa hogar')
            ->set('moneySource', 'Sueldo')
            ->set('balance', '10,50')
            ->call('save')
            ->assertSee('cuenta creada exitosamente')
            ->assertDontSee('De dónde entra el dinero');

        Livewire::test('plan-accounts')
            ->call('openPopup')
            ->call('chooseType', 'indefinida')
            ->set('name', 'Efectivo')
            ->set('moneySource', 'Ventas')
            ->set('balance', '0')
            ->call('save')
            ->assertSee('cuenta creada exitosamente');

        $this->assertSame(2, $latest->cuentas()->count());
        $this->assertSame(0, $older->cuentas()->count());

        $card = $latest->cuentas()->where('type', 'tarjeta_credito')->first();
        $cash = $latest->cuentas()->where('type', 'indefinida')->first();

        $this->assertNotNull($card);
        $this->assertNotNull($cash);
        $this->assertSame('Visa hogar', $card->name);
        $this->assertSame('Sueldo', $card->money_source);
        $this->assertIsString($card->balance);
        $this->assertSame('10.50', $card->balance);
        $this->assertSame('Efectivo', $cash->name);
        $this->assertSame('0.00', $cash->balance);
        $this->assertFalse(Schema::hasColumn('cuentas', 'bank_name'));
    }

    public function test_invalid_popup_values_do_not_create_an_account(): void
    {
        $user = User::factory()->create();

        Plan::factory()->for($user)->create();

        $this->actingAs($user);

        Livewire::test('plan-accounts')
            ->call('openPopup')
            ->call('chooseType', 'tarjeta_credito')
            ->set('name', '')
            ->set('moneySource', 'Sueldo')
            ->set('balance', '10')
            ->call('save')
            ->assertSee('El nombre es obligatorio.')
            ->assertHasErrors(['name' => 'El nombre es obligatorio.']);

        Livewire::test('plan-accounts')
            ->call('openPopup')
            ->call('chooseType', 'indefinida')
            ->set('name', 'Caja')
            ->set('moneySource', '')
            ->set('balance', '10')
            ->call('save')
            ->assertSee('El nombre es obligatorio.');

        Livewire::test('plan-accounts')
            ->call('openPopup')
            ->call('chooseType', 'tarjeta_credito')
            ->set('name', 'Visa')
            ->set('moneySource', 'Sueldo')
            ->set('balance', 'abc')
            ->call('save')
            ->assertSee('El valor no es válido.');

        Livewire::test('plan-accounts')
            ->call('openPopup')
            ->call('chooseType', 'tarjeta_credito')
            ->set('name', 'Visa')
            ->set('moneySource', 'Sueldo')
            ->set('balance', '-5')
            ->call('save')
            ->assertSee('El valor no es válido.');

        $this->assertSame(0, Cuenta::query()->count());
    }

    public function test_edit_icon_updates_the_account_without_opening_its_view(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $plan = Plan::factory()->for($user)->create();
        $cuenta = Cuenta::factory()->for($plan)->create([
            'type' => 'tarjeta_credito',
            'name' => 'Visa hogar',
            'money_source' => 'Sueldo',
            'balance' => '10.00',
        ]);
        $foreign = Cuenta::factory()->for(Plan::factory()->for($other))->create([
            'name' => 'Cuenta ajena',
        ]);

        $this->actingAs($user);

        Livewire::test('plan-accounts')
            ->call('edit', $cuenta->id)
            ->assertSet('step', 'edit')
            ->set('name', 'Visa nueva')
            ->set('moneySource', 'Honorarios')
            ->set('balance', '20,00')
            ->call('save')
            ->assertSet('open', false);

        $cuenta->refresh();

        $this->assertSame('Visa nueva', $cuenta->name);
        $this->assertSame('Honorarios', $cuenta->money_source);
        $this->assertSame('20.00', $cuenta->balance);
        $this->assertSame('tarjeta_credito', $cuenta->type);
        $this->assertIsString($cuenta->balance);

        Livewire::test('plan-accounts')
            ->call('edit', $foreign->id)
            ->assertSet('open', false);

        $this->assertSame('Cuenta ajena', $foreign->fresh()->name);
    }
}
