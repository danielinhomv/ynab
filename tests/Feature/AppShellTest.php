<?php

namespace Tests\Feature;

use DOMDocument;
use DOMElement;
use Tests\TestCase;

class AppShellTest extends TestCase
{
    public function test_home_renders_livewire_shell_with_empty_header_and_aside(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('livewire.js', false);
        $response->assertDontSee('Dinero por asignar');
        $response->assertDontSee('Nuevo sobre');
        $response->assertDontSee('Crear cuenta');
        $response->assertDontSee('Let\'s get started');
        $response->assertDontSee('type="password"', false);

        $document = new DOMDocument;
        $loaded = $document->loadHTML($response->getContent(), LIBXML_NOERROR);

        $this->assertTrue($loaded);

        $header = $document->getElementsByTagName('header')->item(0);
        $aside = $document->getElementsByTagName('aside')->item(0);
        $main = $document->getElementsByTagName('main')->item(0);

        $this->assertInstanceOf(DOMElement::class, $header);
        $this->assertInstanceOf(DOMElement::class, $aside);
        $this->assertInstanceOf(DOMElement::class, $main);

        $this->assertRegionIsEmptyOfNavigation($header);
        $this->assertRegionIsEmptyOfNavigation($aside);
    }

    private function assertRegionIsEmptyOfNavigation(DOMElement $region): void
    {
        $this->assertSame(0, $region->getElementsByTagName('a')->length);
        $this->assertSame(0, $region->getElementsByTagName('nav')->length);
        $this->assertSame(0, $region->getElementsByTagName('ul')->length);
        $this->assertSame(0, $region->getElementsByTagName('ol')->length);
        $this->assertSame(0, $region->getElementsByTagName('button')->length);
        $this->assertSame(0, $region->getElementsByTagName('form')->length);
        $this->assertSame('', trim($region->textContent));
    }
}
