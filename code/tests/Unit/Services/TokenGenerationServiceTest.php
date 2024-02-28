<?php
declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\TokenGenerationService;
use Tests\TestCase;

/**
 * Class TokenGenerationServiceTest
 * @package Tests\Unit\Services
 */
class TokenGenerationServiceTest extends TestCase
{
    public function testGenerateToken(): void
    {
        $service = new TokenGenerationService();

        $this->assertEquals(40, strlen($service->generateToken()));
        $this->assertEquals(54, strlen($service->generateToken(54)));
    }
}