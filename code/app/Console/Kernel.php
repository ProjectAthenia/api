<?php
declare(strict_types=1);

namespace App\Console;

use App\Athenia\Console\BaseKernel;
use Illuminate\Console\Scheduling\Schedule;

/**
 * Class Kernel
 * @package App\Console
 */
class Kernel extends BaseKernel
{
    /**
     * Gets the commands path for the child app
     *
     * @return string
     */
    public function getAppCommandsPath(): string
    {
        return __DIR__.'./Commands';
    }

    /**
     * @param Schedule $schedule
     */
    protected function schedule(Schedule $schedule)
    {
    }
}
