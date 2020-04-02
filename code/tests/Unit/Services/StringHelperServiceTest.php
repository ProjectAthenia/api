<?php
declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\StringHelperService;
use Tests\TestCase;

/**
 * Class StringHelperServiceTest
 * @package Tests\Unit\Services
 */
class StringHelperServiceTest extends TestCase
{
    public function testMbSubstrReplace()
    {
        $service = new StringHelperService();

        $result = $service->mbSubstrReplace('你好，王', '李', 3, 1);

        $this->assertEquals('你好，李', $result);
    }
}