<?php

namespace Tests\Feature;

use App\Models\Sobre;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PlanSchemaTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_plan_migration_has_one_currency_column(): void
    {
        $this->assertTrue(Schema::hasTable('plans'));

        $columns = Schema::getColumnListing('plans');

        $this->assertContains('user_id', $columns);
        $this->assertContains('name', $columns);
        $this->assertContains('currency', $columns);
        $this->assertContains('fecha', $columns);
        $this->assertContains('number_format', $columns);
        $this->assertContains('currency_placement', $columns);
        $this->assertSame(
            ['currency', 'currency_placement'],
            array_values(array_filter(
                $columns,
                fn (string $column): bool => str_contains($column, 'currency'),
            )),
        );

        $sobre = Sobre::factory()->create();

        $this->assertIsString($sobre->category->plan->currency);
        $this->assertIsString($sobre->assigned);
    }

    public function test_sobre_amounts_are_decimal_not_float(): void
    {
        $this->assertTrue(Schema::hasColumns('sobres', ['assigned', 'activity', 'available']));

        foreach (['assigned', 'activity', 'available'] as $column) {
            $type = Schema::getColumnType('sobres', $column);

            $this->assertContains($type, ['decimal', 'numeric']);
            $this->assertNotContains($type, ['float', 'double', 'real']);
        }

        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $types = DB::table('information_schema.columns')
            ->where('table_name', 'sobres')
            ->whereIn('column_name', ['assigned', 'activity', 'available'])
            ->pluck('data_type');

        $this->assertCount(3, $types);
        $this->assertTrue($types->every(fn (string $type): bool => $type === 'numeric'));
    }
}
