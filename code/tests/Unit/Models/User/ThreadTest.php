<?php
declare(strict_types=1);

namespace Tests\Unit\Models\User;

use App\Models\User\Thread;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

/**
 * Class ThreadTest
 * @package Tests\Unit\Models\User
 */
class ThreadTest extends TestCase
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