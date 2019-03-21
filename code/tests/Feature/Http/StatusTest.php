<?php
/**
 * Feature test for the status controller
 */
declare(strict_types=1);

namespace Tests\Feature\Http;

use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class StatusTest
 * @package Tests\Feature\Http
 */
class StatusTest extends TestCase
{
    use MocksApplicationLog;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockApplicationLog();
    }

    public function testSuccess()
    {
        $response = $this->get('/v1/status');

        $response->assertStatus(200);
        $response->assertExactJson([
            'status' => 'ok',
        ]);
    }
}