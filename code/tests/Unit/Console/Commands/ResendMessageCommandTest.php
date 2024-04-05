<?php
declare(strict_types=1);

namespace Tests\Unit\Console\Commands;

use App\Console\Commands\ResendMessageCommand;
use App\Contracts\Repositories\User\MessageRepositoryContract;
use App\Models\User\Message;
use Illuminate\Contracts\Events\Dispatcher;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use Symfony\Component\Console\Input\InputInterface;
use Tests\CustomMockInterface;
use Tests\TestCase;
use Tests\Traits\MocksConsoleOutput;

/**
 * Class ResendMessageCommandTest
 * @package Tests\Integration\Console\Commands
 */
final class ResendMessageCommandTest extends TestCase
{
    use MocksConsoleOutput;

    /**
     * @var MessageRepositoryContract|array|LegacyMockInterface|MockInterface|CustomMockInterface
     */
    private $messageRepository;

    /**
     * @var array|Dispatcher|LegacyMockInterface|MockInterface|CustomMockInterface
     */
    private $dispatcher;

    /**
     * @var ResendMessageCommand
     */
    private ResendMessageCommand $command;

    protected function setUp(): void
    {
        parent::setUp();

        $this->messageRepository = mock(MessageRepositoryContract::class);
        $this->dispatcher = mock(Dispatcher::class);
        $this->command = new ResendMessageCommand($this->messageRepository, $this->dispatcher);
        $this->mockConsoleOutput($this->command);
    }

    public function testHandle(): void
    {
        $reflected = new \ReflectionClass($this->command);
        $input = $reflected->getProperty('input');
        $input->setAccessible(true);
        $mockInput = mock(InputInterface::class);
        $mockInput->shouldReceive('getArgument')->andReturn(4);
        $input->setValue($this->command, $mockInput);

        $this->messageRepository->shouldReceive('findOrFail')->andReturn(new Message());
        $this->dispatcher->shouldReceive('dispatch');

        $this->command->handle();
    }
}
