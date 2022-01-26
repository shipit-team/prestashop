<?php
namespace Tests\Unit\Foo;

use PHPUnit\Framework\TestCase;

class RateTest extends TestCase
{
    $integration = new ShipitIntegrationCore();
    $response = $integrations->rates($params);
    $response->assertStatus(200);

}