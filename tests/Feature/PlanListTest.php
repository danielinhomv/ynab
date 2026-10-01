<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\User;
use DOMDocument;
use DOMElement;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class PlanListTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_user_sees_name_and_single_currency_and_not_another_users_plan(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        Plan::factory()->for($user)->create([
            'name' => 'Casa',
            'currency' => 'BOB',
        ]);
        Plan::factory()->for($user)->create([
            'name' => 'Viaje',
            'currency' => 'USD',
        ]);
        Plan::factory()->for($other)->create([
            'name' => 'Ajeno',
            'currency' => 'EUR',
        ]);

        $this->actingAs($user);

        Livewire::test('plan-list')
            ->assertSee('Casa')
            ->assertSee('BOB')
            ->assertSee('Viaje')
            ->assertSee('USD')
            ->assertDontSee('Ajeno')
            ->assertDontSee('EUR');
    }

    public function test_guest_does_not_see_the_list(): void
    {
        $user = User::factory()->create();

        Plan::factory()->for($user)->create([
            'name' => 'Casa',
            'currency' => 'BOB',
        ]);

        Livewire::test('plan-list')
            ->assertDontSee('Casa')
            ->assertDontSee('BOB')
            ->assertDontSee('Aún no tienes planes.');

        $this->get('/')->assertDontSee('Aún no tienes planes.');
    }

    public function test_one_click_opens_the_plan_and_a_foreign_plan_stays_closed(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $plan = Plan::factory()->for($user)->create([
            'name' => 'Casa',
            'currency' => 'BOB',
        ]);
        $foreign = Plan::factory()->for($other)->create([
            'name' => 'Ajeno',
            'currency' => 'EUR',
        ]);

        $this->actingAs($user);

        Livewire::test('plan-list')
            ->call('open', $plan->id)
            ->assertSet('openPlanId', $plan->id)
            ->assertSeeHtml('bg-mint')
            ->assertDontSee('Abrir');

        Livewire::test('plan-list')
            ->call('open', $foreign->id)
            ->assertSet('openPlanId', $plan->id);

        $this->assertFalse(Schema::hasColumn('plans', 'last_opened_at'));
    }

    public function test_create_button_is_visible_with_plans_and_does_not_change_currency(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->for($user)->create([
            'name' => 'Casa',
            'currency' => 'BOB',
        ]);

        $this->actingAs($user);

        Livewire::test('plan-list')
            ->call('open', $plan->id)
            ->assertSee('Crear plan')
            ->assertSeeHtml(route('planes.crear'))
            ->assertDontSee('Number format')
            ->assertDontSee('Currency placement');

        $this->assertSame('BOB', $plan->fresh()->currency);
    }

    public function test_empty_state_shows_the_message_and_the_create_button(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test('plan-list')
            ->assertSee('Aún no tienes planes.')
            ->assertSee('Crear plan')
            ->assertSeeHtml(route('planes.crear'))
            ->assertDontSee('BOB');
    }

    public function test_list_is_visible_at_the_top_of_the_authenticated_dashboard(): void
    {
        $user = User::factory()->create();

        Plan::factory()->for($user)->create([
            'name' => 'Casa',
            'currency' => 'BOB',
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertOk();
        $response->assertSee('Casa');
        $response->assertSee('BOB');
        $response->assertSee('Crear plan');
        $response->assertSee('Dinero por asignar');
        $response->assertSee('Plan activo');
        $response->assertSee('Presupuesto Mensual');

        $document = new DOMDocument;
        $loaded = $document->loadHTML($response->getContent(), LIBXML_NOERROR);

        $this->assertTrue($loaded);

        $main = $document->getElementsByTagName('main')->item(0);
        $aside = $document->getElementsByTagName('aside')->item(0);

        $this->assertInstanceOf(DOMElement::class, $main);
        $this->assertInstanceOf(DOMElement::class, $aside);
        $this->assertStringContainsString('Casa', $main->textContent);
        $this->assertStringNotContainsString('Casa', $aside->textContent);
    }

    public function test_empty_dashboard_shows_the_empty_state_without_replacing_the_budget(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/')
            ->assertOk()
            ->assertSee('Aún no tienes planes.')
            ->assertSee('Crear plan')
            ->assertSee('Dinero por asignar')
            ->assertSee('Plan activo');
    }
}
