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
final class StatusTest extends TestCase
{
    use MocksApplicationLog;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockApplicationLog();
    }

    public function testSuccess(): void
    {
        $response = $this->get('/v1/status');

        $response->assertStatus(200);
        $response->assertSimilarJson([
            'status' => 'ok',
        ]);
    }
}
