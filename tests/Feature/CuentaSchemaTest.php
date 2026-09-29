<?php

namespace Tests\Feature;

use App\Models\Cuenta;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CuentaSchemaTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_cuenta_balance_is_decimal_and_has_no_bank_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('cuentas', [
            'plan_id',
            'type',
            'name',
            'money_source',
            'balance',
        ]));

        $columns = Schema::getColumnListing('cuentas');

        $this->assertNotContains('bank', $columns);
        $this->assertNotContains('bank_name', $columns);
        $this->assertNotContains('account_number', $columns);

        $type = Schema::getColumnType('cuentas', 'balance');

        $this->assertContains($type, ['decimal', 'numeric']);
        $this->assertNotContains($type, ['float', 'double', 'real']);

        $cuenta = Cuenta::factory()->create([
            'balance' => '10.50',
        ]);

        $this->assertIsString($cuenta->balance);
        $this->assertSame('10.50', $cuenta->balance);

        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $dataType = DB::table('information_schema.columns')
            ->where('table_name', 'cuentas')
            ->where('column_name', 'balance')
            ->value('data_type');

        $this->assertSame('numeric', $dataType);
    }
}
