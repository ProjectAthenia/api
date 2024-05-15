<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Models;

use App\Models\Category;
use Tests\TestCase;

/**
 * Class CategoryTest
 * @package Tests\Athenia\Unit\Models
 */
final class CategoryTest extends TestCase
{
    public function testNewQueryAddsDefaultOrder(): void
    {
        $model = new Category();
        $query = $model->newQuery();

        $this->assertStringContainsString('order by `name`', $query->toSql());
    }
}