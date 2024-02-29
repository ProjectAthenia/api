<?php
declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Jobs\CanDisplayOutputAbstractJob;
use Illuminate\Console\OutputStyle;
use Symfony\Component\Console\Helper\ProgressBar;
use Tests\TestCase;

final class CanDisplayOutputAbstractJobTest extends TestCase
{
    public function testOutMessageDoesNothingWithoutOutput(): void
    {
        /** @var CanDisplayOutputAbstractJob $job */
        $job = $this->getMockForAbstractClass(CanDisplayOutputAbstractJob::class);

        $job->outputMessage("hello");
    }

    public function testOutMessageWritesWhenOutputExists(): void
    {
        $output = mock(OutputStyle::class);

        /** @var CanDisplayOutputAbstractJob $job */
        $job = $this->getMockForAbstractClass(CanDisplayOutputAbstractJob::class, [$output]);

        $output->shouldReceive('text');

        $job->outputMessage("hello");
    }

    public function testCreateProgressBarDoesNothingWithoutOutput(): void
    {
        /** @var CanDisplayOutputAbstractJob $job */
        $job = $this->getMockForAbstractClass(CanDisplayOutputAbstractJob::class);

        $job->createProgress("progress", 100);
    }

    public function testCreateProgressBarWhenOutputExists(): void
    {
        $output = mock(OutputStyle::class);

        /** @var CanDisplayOutputAbstractJob $job */
        $job = $this->getMockForAbstractClass(CanDisplayOutputAbstractJob::class, [$output]);

        $output->shouldReceive('isDecorated')->andReturn(false);

        $progress = mock(new ProgressBar($output));
        $output->shouldReceive('createProgressBar')->andReturn($progress);

        $job->createProgress("progress", 100);
    }

    public function testAdvanceProgressDoesNothingBeforeCreation(): void
    {
        $output = mock(OutputStyle::class);

        /** @var CanDisplayOutputAbstractJob $job */
        $job = $this->getMockForAbstractClass(CanDisplayOutputAbstractJob::class, [$output]);

        $output->shouldReceive('isDecorated')->andReturn(false);

        $job->advanceProgress("progress");
    }

    public function testAdvanceProgressInteractsProperly(): void
    {
        $output = mock(OutputStyle::class);

        /** @var CanDisplayOutputAbstractJob $job */
        $job = $this->getMockForAbstractClass(CanDisplayOutputAbstractJob::class, [$output]);

        $output->shouldReceive('isDecorated')->andReturn(false);

        $progress = mock(new ProgressBar($output));
        $output->shouldReceive('createProgressBar')->andReturn($progress);
        $progress->shouldReceive('advance');

        $job->advanceProgress("progress");
    }
}