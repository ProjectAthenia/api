<?php
declare(strict_types=1);

namespace App\Athenia\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

abstract class BaseKernel extends ConsoleKernel
{
    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load([
            $this->getAppCommandsPath(),
            __DIR__.'/Commands',
        ]);
    }

    /**
     * Gets the commands path for the child app
     *
     * @return string
     */
    public abstract function getAppCommandsPath(): string;
}