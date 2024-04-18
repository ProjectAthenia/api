<?php
declare(strict_types=1);

namespace Tests\Unit\Models\User;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User\Message;
use Tests\TestCase;

/**
 * Class MessageTest
 * @package Tests\Unit\Models\User
 */
final class MessageTest extends TestCase
{
    public function testFrom(): void
    {
        $message = new Message();
        $relation = $message->from();

        $this->assertEquals('users.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('messages.from_id', $relation->getQualifiedForeignKeyName());
        $this->assertEquals('from_type', $relation->getMorphType());
    }

    public function testThread(): void
    {
        $message = new Message();
        $relation = $message->thread();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('threads.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('messages.thread_id', $relation->getQualifiedForeignKeyName());
    }

    public function testTo(): void
    {
        $message = new Message();
        $relation = $message->to();

        $this->assertEquals('users.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('messages.to_id', $relation->getQualifiedForeignKeyName());
        $this->assertEquals('to_type', $relation->getMorphType());
    }
}