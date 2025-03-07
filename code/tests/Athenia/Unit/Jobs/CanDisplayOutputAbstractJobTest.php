<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Jobs;

use Illuminate\Console\OutputStyle;
use Symfony\Component\Console\Helper\ProgressBar;
use Tests\Mocks\CanDisplayOutputJob;
use Tests\TestCase;

final class CanDisplayOutputAbstractJobTest extends TestCase
{
    public function testOutMessageDoesNothingWithoutOutput(): void
    {
        $job = new CanDisplayOutputJob();

        $job->outputMessage("hello");
    }

    public function testOutMessageWritesWhenOutputExists(): void
    {
        $output = mock(OutputStyle::class);

        $job = new CanDisplayOutputJob($output);

        $output->shouldReceive('text');

        $job->outputMessage("hello");
    }

    public function testCreateProgressBarDoesNothingWithoutOutput(): void
    {
        $job = new CanDisplayOutputJob();

        $job->createProgress("progress", 100);
    }

    public function testCreateProgressBarWhenOutputExists(): void
    {
        $output = mock(OutputStyle::class);

        $job = new CanDisplayOutputJob($output);

        $output->shouldReceive('isDecorated')->andReturn(false);

        $progress = mock(new ProgressBar($output));
        $output->shouldReceive('createProgressBar')->andReturn($progress);

        $job->createProgress("progress", 100);
    }

    public function testAdvanceProgressDoesNothingBeforeCreation(): void
    {
        $output = mock(OutputStyle::class);

        $job = new CanDisplayOutputJob($output);

        $output->shouldReceive('isDecorated')->andReturn(false);

        $job->advanceProgress("progress");
    }

    public function testAdvanceProgressInteractsProperly(): void
    {
        $output = mock(OutputStyle::class);

        $job = new CanDisplayOutputJob($output);

        $output->shouldReceive('isDecorated')->andReturn(false);

        $progress = mock(new ProgressBar($output));
        $output->shouldReceive('createProgressBar')->andReturn($progress);
        $progress->shouldReceive('advance');

        $job->advanceProgress("progress");
    }
}