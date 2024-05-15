<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Models\Messaging;

use App\Models\Messaging\Thread;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

/**
 * Class ThreadTest
 * @package Tests\Athenia\Unit\Models\User
 */
final class ThreadTest extends TestCase
{
    public function testMessages(): void
    {
        $user = new Thread();
        $relation = $user->messages();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertEquals('threads.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('messages.thread_id', $relation->getQualifiedForeignKeyName());
    }

    public function testUsers(): void
    {
        $model = new Thread();
        $relation = $model->users();

        $this->assertEquals('thread_user', $relation->getTable());
        $this->assertEquals('threads.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('thread_user.thread_id', $relation->getQualifiedForeignPivotKeyName());
        $this->assertEquals('thread_user.user_id', $relation->getQualifiedRelatedPivotKeyName());
    }
}