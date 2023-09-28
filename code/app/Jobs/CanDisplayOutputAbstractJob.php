<?php
declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Console\OutputStyle;
use Symfony\Component\Console\Helper\ProgressBar;

abstract class CanDisplayOutputAbstractJob
{
    /**
     * @var array|ProgressBar[]
     */
    private $progressBars = [];

    /**
     * @param OutputStyle|null $output
     */
    public function __construct(protected ?OutputStyle $output = null)
    {}

    /**
     * Outputs a message if our output exists
     *
     * @param string $message
     * @return void
     */
    public function outputMessage(string $message)
    {
        $this->output?->text($message);
    }

    /**
     * Creates a progress bar for us. This must be done before advancing a progress bar
     *
     * @param string $name
     * @param int $steps
     * @return void
     */
    public function createProgress(string $name, int $steps)
    {
        $this->progressBars[$name] = $this->output?->createProgressBar($steps);
    }

    /**
     * Advances a progress bar if it exists
     *
     * @param string $name
     * @return void
     */
    public function advanceProgress(string $name)
    {
        ($this->progressBars[$name] ?? null)?->advance();
    }
}