<?php
/**
 * Created by PhpStorm.
 * User: matsui
 * Date: 2017/09/26
 * Time: 16:31
 */

namespace Tests\Feature;

use App;
use Crhg\LaravelIRKit\Facades\IRKit;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ThermostatApiTest extends TestCase
{
    use DatabaseMigrations {
        DatabaseMigrations::runDatabaseMigrations as runDataBaseMigrationsOfTrait;
    }

    public function testDummy()
    {
        $this->assertTrue(true);
    }

    public function testStatus()
    {
        $name = 'thermostat1';
        $response = $this->get("/api/thermostat/$name/status");
        $response->assertStatus(200);
    }

    public function testTargetHeatingCoolingState()
    {
        $name = 'thermostat1';
        $response = $this->get("/api/thermostat/$name/status");
        $response->assertStatus(200);

        IRKit::shouldReceive('send')
            ->once()
            ->with('aircon', 'h25');
        $response = $this->get("/api/thermostat/$name/targetHeatingCoolingState/1");
        $response->assertStatus(200);

        $response = $this->get("/api/thermostat/$name/status");
        $response->assertStatus(200);
        $response->assertJson(['targetHeatingCoolingState' => 1]);
    }

    public function testTargetTemperature()
    {
        $name = 'thermostat1';
        $response = $this->get("/api/thermostat/$name/status");
        $response->assertStatus(200);

        $response = $this->get("/api/thermostat/$name/targetTemperature/23.0");
        $response->assertStatus(200);

        $response = $this->get("/api/thermostat/$name/status");
        $response->assertStatus(200);
        $response->assertJson(
            [
                'targetHeatingCoolingState' => 0,
                'targetTemperature'        => 23.0,
            ]
        );
    }

    public function runDatabaseMigrations()
    {
        $database_file = tempnam(sys_get_temp_dir(), 'database.sqlite');
        \Config::set('database.connections.sqlite.database', $database_file);
        $this->runDataBaseMigrationsOfTrait();
        $this->beforeApplicationDestroyed(function () use ($database_file) {
            unlink($database_file);
        });
    }
}