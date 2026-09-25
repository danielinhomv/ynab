<?php

namespace Tests\Feature;

use Tests\TestCase;

class PostgreSqlConfigurationTest extends TestCase
{
    public function test_example_environment_declares_postgresql_not_sqlite(): void
    {
        $example = file_get_contents(base_path('.env.example'));

        $this->assertNotFalse($example);
        $this->assertMatchesRegularExpression('/^DB_CONNECTION=pgsql$/m', $example);
        $this->assertDoesNotMatchRegularExpression('/^DB_CONNECTION=sqlite$/m', $example);
        $this->assertMatchesRegularExpression('/^DB_HOST=/m', $example);
        $this->assertMatchesRegularExpression('/^DB_PORT=5432$/m', $example);
        $this->assertMatchesRegularExpression('/^DB_DATABASE=/m', $example);
        $this->assertMatchesRegularExpression('/^DB_USERNAME=/m', $example);
        $this->assertMatchesRegularExpression('/^DB_PASSWORD=/m', $example);
    }

    public function test_test_suite_uses_sqlite_in_memory(): void
    {
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
    }
}
