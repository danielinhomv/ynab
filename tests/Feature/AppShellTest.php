<?php

namespace Tests\Feature;

use App\Models\User;
use DOMDocument;
use DOMElement;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AppShellTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_authenticated_home_renders_dashboard_not_landing(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertOk();
        $response->assertSee('livewire.js', false);
        $response->assertSee('Cerrar sesión');
        $response->assertSee('Configuración');
        $response->assertSee('Dinero por asignar');
        $response->assertSee('Presupuesto Mensual');
        $response->assertSee('Nuevo sobre');
        $response->assertDontSee('Crear cuenta');
        $response->assertDontSee('Dale un trabajo a cada');
        $response->assertDontSee('Confirmar contraseña');

        $document = new DOMDocument;
        $loaded = $document->loadHTML($response->getContent(), LIBXML_NOERROR);

        $this->assertTrue($loaded);

        $header = $document->getElementsByTagName('header')->item(0);
        $aside = $document->getElementsByTagName('aside')->item(0);
        $main = $document->getElementsByTagName('main')->item(0);

        $this->assertInstanceOf(DOMElement::class, $header);
        $this->assertInstanceOf(DOMElement::class, $aside);
        $this->assertInstanceOf(DOMElement::class, $main);
        $this->assertStringContainsString('Efectivo', $aside->textContent);
        $this->assertStringContainsString('Cerrar sesión', $header->textContent);
        $this->assertStringContainsString('Configuración', $header->textContent);
        $this->assertSame(1, $header->getElementsByTagName('details')->length);
        $this->assertGreaterThan(0, $header->getElementsByTagName('button')->length);

        $headerHrefs = [];
        foreach ($header->getElementsByTagName('a') as $anchor) {
            $headerHrefs[] = $anchor->getAttribute('href');
        }

        $this->assertFalse(
            collect($headerHrefs)->contains(fn (string $href): bool => str_contains($href, 'configuracion')),
        );
    }

    public function test_guest_home_shows_landing_not_dashboard(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Crear cuenta');
        $response->assertSee('Iniciar sesión');
        $response->assertDontSee('Confirmar contraseña');
        $response->assertSee('Dale un trabajo a cada');
        $response->assertDontSee('Cerrar sesión');
        $response->assertDontSee('Dinero por asignar');
        $response->assertDontSee('Presupuesto Mensual');
        $response->assertDontSee('Agregar cuenta');

        $document = new DOMDocument;
        $loaded = $document->loadHTML($response->getContent(), LIBXML_NOERROR);

        $this->assertTrue($loaded);
        $this->assertNull($document->getElementsByTagName('aside')->item(0));
    }
}
