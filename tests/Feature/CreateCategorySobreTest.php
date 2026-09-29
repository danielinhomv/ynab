<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Plan;
use App\Models\Sobre;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CreateCategorySobreTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_named_category_appears_on_the_latest_plan_without_leaving_the_page(): void
    {
        $user = User::factory()->create();
        $older = Plan::factory()->for($user)->create();
        $latest = Plan::factory()->for($user)->create();

        $this->actingAs($user);

        Livewire::test('plan-sobres')
            ->call('abrirPlan', $latest->id)
            ->dispatch('open-category')
            ->assertSet('creating', true)
            ->set('draftName', 'Transporte')
            ->call('store')
            ->assertSet('creating', false)
            ->assertSee('Transporte')
            ->assertNoRedirect()
            ->assertDontSee('Eliminar');

        $category = Category::query()->where('name', 'Transporte')->first();

        $this->assertNotNull($category);
        $this->assertSame($latest->id, $category->plan_id);
        $this->assertNotSame($older->id, $category->plan_id);

        $this->get('/')
            ->assertOk()
            ->assertSee('Dinero por asignar')
            ->assertSee('Bs 1.250,00')
            ->assertDontSee('Eliminar');
    }

    public function test_empty_category_name_shows_the_error_and_creates_nothing(): void
    {
        $user = User::factory()->create();
        Plan::factory()->for($user)->create();

        $this->actingAs($user);

        Livewire::test('plan-sobres')
            ->call('openCategory')
            ->set('draftName', '')
            ->call('store')
            ->assertHasErrors(['draftName' => 'required'])
            ->assertSee('El nombre es obligatorio.')
            ->assertSet('creating', true);

        $this->assertSame(0, Category::query()->count());
    }

    public function test_guest_does_not_create_a_category(): void
    {
        Livewire::test('plan-sobres')
            ->call('openCategory')
            ->assertSet('creating', false)
            ->set('draftName', 'Transporte')
            ->set('creating', true)
            ->call('store');

        $this->assertSame(0, Category::query()->count());
    }

    public function test_home_buttons_dispatch_the_create_actions(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/')
            ->assertOk()
            ->assertSeeHtml("\$dispatchTo('plan-sobres', 'open-category')")
            ->assertSeeHtml("\$dispatchTo('plan-sobres', 'open-sobre')");
    }

    public function test_single_category_is_preselected_for_a_new_sobre(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->for($user)->create();
        $category = Category::factory()->for($plan)->create(['name' => 'Casa']);

        $this->actingAs($user);

        Livewire::test('plan-sobres')
            ->dispatch('open-sobre')
            ->assertSet('categoryId', $category->id)
            ->assertSeeHtml('selected');
    }

    public function test_latest_category_is_preselected_when_the_plan_has_several(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->for($user)->create();
        Category::factory()->for($plan)->create(['name' => 'Vieja']);
        $newer = Category::factory()->for($plan)->create(['name' => 'Nueva']);

        $this->actingAs($user);

        Livewire::test('plan-sobres')
            ->call('openSobre')
            ->assertSet('categoryId', $newer->id);
    }

    public function test_new_sobre_stores_zero_decimal_amounts_and_shows_the_row(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->for($user)->create([
            'currency' => 'BOB',
            'number_format' => '1.234,56',
            'currency_placement' => 'antes',
        ]);
        $category = Category::factory()->for($plan)->create(['name' => 'Casa']);

        $this->actingAs($user);

        Livewire::test('plan-sobres')
            ->call('openSobre')
            ->set('draftName', 'Alquiler')
            ->call('store')
            ->assertSet('creating', false)
            ->assertSee('Alquiler')
            ->assertSee('Bs 0,00')
            ->assertNoRedirect()
            ->assertDontSee('Eliminar');

        $sobre = Sobre::query()->where('name', 'Alquiler')->first();

        $this->assertNotNull($sobre);
        $this->assertSame($category->id, $sobre->category_id);
        $this->assertIsString($sobre->assigned);
        $this->assertIsString($sobre->activity);
        $this->assertIsString($sobre->available);
        $this->assertSame('0.00', $sobre->assigned);
        $this->assertSame('0.00', $sobre->activity);
        $this->assertSame('0.00', $sobre->available);
    }

    public function test_empty_sobre_name_shows_the_error_and_creates_nothing(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->for($user)->create();
        Category::factory()->for($plan)->create();

        $this->actingAs($user);

        Livewire::test('plan-sobres')
            ->call('openSobre')
            ->set('draftName', '')
            ->call('store')
            ->assertHasErrors(['draftName' => 'required'])
            ->assertSee('El nombre es obligatorio.')
            ->assertSet('creating', true);

        $this->assertSame(0, Sobre::query()->count());
    }

    public function test_sobre_is_not_created_on_another_users_category(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $plan = Plan::factory()->for($user)->create();
        Category::factory()->for($plan)->create(['name' => 'Casa']);
        $foreign = Category::factory()->for(Plan::factory()->for($other))->create();

        $this->actingAs($user);

        Livewire::test('plan-sobres')
            ->call('openSobre')
            ->set('draftName', 'Ajeno')
            ->set('categoryId', $foreign->id)
            ->call('store')
            ->assertSee('El valor no es válido.')
            ->assertDontSee('Ajeno');

        $this->assertSame(0, Sobre::query()->count());
        $this->assertSame(0, $foreign->sobres()->count());
    }

    public function test_sobre_popup_creates_a_category_without_leaving_when_none_exist(): void
    {
        $user = User::factory()->create();
        Plan::factory()->for($user)->create();

        $this->actingAs($user);

        $component = Livewire::test('plan-sobres')
            ->call('openSobre')
            ->assertSee('+ Nueva categoría')
            ->call('openCategoryFromSobre')
            ->set('draftName', 'Casa')
            ->call('store')
            ->assertSet('creating', true)
            ->assertSet('createKind', 'sobre')
            ->assertSee('Casa');

        $category = Category::query()->where('name', 'Casa')->first();

        $this->assertNotNull($category);
        $component->assertSet('categoryId', $category->id);

        $component
            ->set('draftName', 'Luz')
            ->call('store')
            ->assertSee('Luz')
            ->assertSet('creating', false);

        $sobre = Sobre::query()->where('name', 'Luz')->first();

        $this->assertNotNull($sobre);
        $this->assertSame($category->id, $sobre->category_id);
        $this->assertSame('0.00', $sobre->assigned);
    }

    public function test_buttons_do_not_create_a_plan_when_the_user_has_none(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test('plan-sobres')
            ->call('openCategory')
            ->assertSee('Aún no tienes planes.')
            ->set('draftName', 'Casa')
            ->call('store')
            ->call('openSobre')
            ->set('draftName', 'Luz')
            ->call('store');

        $this->assertSame(0, Plan::query()->count());
        $this->assertSame(0, Category::query()->count());
        $this->assertSame(0, Sobre::query()->count());
    }

    public function test_category_name_is_escaped_in_the_table(): void
    {
        $user = User::factory()->create();
        Plan::factory()->for($user)->create();

        $this->actingAs($user);

        $html = Livewire::test('plan-sobres')
            ->call('openCategory')
            ->set('draftName', "<script>alert('x')</script>")
            ->call('store')
            ->html();

        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringNotContainsString("<script>alert('x')</script>", $html);
    }
}
