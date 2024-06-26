<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Services;

use App\Athenia\Services\StringHelperService;
use Tests\TestCase;

/**
 * Class StringHelperServiceTest
 * @package Tests\Athenia\Unit\Services
 */
final class StringHelperServiceTest extends TestCase
{
    public function testMbSubstrReplace(): void
    {
        $service = new StringHelperService();

        $result = $service->mbSubstrReplace('你好，王', '李', 3, 1);

        $this->assertEquals('你好，李', $result);
    }

    public function testHasDomainName()
    {
        $service = new StringHelperService();

        $this->assertTrue($service->hasDomainName('hello https://welcome.bye'));
        $this->assertFalse($service->hasDomainName('hello welcome'));
    }
}