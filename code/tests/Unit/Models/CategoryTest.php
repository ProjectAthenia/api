<?php
declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Category;
use Tests\TestCase;

/**
 * Class CategoryTest
 * @package Tests\Unit\Models
 */
class CategoryTest extends TestCase
{
    public function testNewQueryAddsDefaultOrder()
    {
        $model = new Category();
        $query = $model->newQuery();

        $this->assertStringContainsString('order by `name`', $query->toSql());
    }
}