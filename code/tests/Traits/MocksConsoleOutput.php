<?php
declare(strict_types=1);

namespace Tests\Traits;

use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\OutputStyle;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Trait MocksConsoleOutput
 * @package Tests\Traits
 */
trait MocksConsoleOutput
{
    /**
     * Replaces the instance of the output with something that will let everything pass
     */
    protected function mockConsoleOutput(Command $command)
    {
        $reflected = new \ReflectionClass($command);
        $output = $reflected->getProperty('output');
        $output->setAccessible(true);
        $mockOutput = mock(SymfonyStyle::class);

        $progressMock = mock(ProgressBar::class);
        $progressMock->shouldIgnoreMissing();

        $mockOutput->shouldIgnoreMissing($progressMock);

        $output->setValue($command, $mockOutput);
    }
}
