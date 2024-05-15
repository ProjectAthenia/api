<?php
declare(strict_types=1);

namespace Tests\Unit\Models\Payment;

use App\Models\Payment\LineItem;
use Tests\TestCase;

/**
 * Class LineItemTest
 * @package Tests\Unit\Models\Payment
 */
final class LineItemTest extends TestCase
{
    public function testItem(): void
    {
        $model = new LineItem();
        $relation = $model->item();

        $this->assertEquals('line_items.item_id', $relation->getQualifiedForeignKeyName());
        $this->assertEquals('item_type', $relation->getMorphType());
    }

    public function testPayment(): void
    {
        $model = new LineItem();
        $relation = $model->payment();

        $this->assertEquals('payments.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('line_items.payment_id', $relation->getQualifiedForeignKeyName());
    }
}