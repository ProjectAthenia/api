<?php
declare(strict_types=1);

namespace App\Listeners\Article;

use App\Contracts\Repositories\Wiki\ArticleVersionRepositoryContract;
use App\Contracts\Services\ArticleVersionCalculationServiceContract;
use App\Events\Article\ArticleVersionCreatedEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class ArticleVersionCreatedListener
 * @package App\Listeners\Article
 */
class ArticleVersionCreatedListener implements ShouldQueue
{
    use Queueable;

    /**
     * @var ArticleVersionRepositoryContract
     */
    private $repository;

    /**
     * @var ArticleVersionCalculationServiceContract
     */
    private $calculationService;

    /**
     * ArticleVersionCreatedListener constructor.
     * @param ArticleVersionRepositoryContract $repository
     * @param ArticleVersionCalculationServiceContract $calculationService
     */
    public function __construct(ArticleVersionRepositoryContract $repository,
                                ArticleVersionCalculationServiceContract $calculationService)
    {
        $this->repository = $repository;
        $this->calculationService = $calculationService;
    }

    /**
     * @param ArticleVersionCreatedEvent $event
     */
    public function handle(ArticleVersionCreatedEvent $event)
    {
        $newVersion = $event->getNewVersion();
        $oldVersion = $event->getOldVersion();

        $major = 1;
        $minor = 0;
        $patch = 0;

        if ($oldVersion && $oldVersion->name) {

            $oldVersionNumber = explode('.', $oldVersion->name);

            if (count($oldVersionNumber) >= 3) {

                $newVersionContent = $newVersion->iteration ? $newVersion->iteration->content : null;
                $oldVersionContent = $oldVersion->iteration ? $oldVersion->iteration->content : null;

                $major = $oldVersionNumber[0] - 0;
                $minor = $oldVersionNumber[1] - 0;
                $patch = $oldVersionNumber[2] - 0;

                switch (true) {
                    case $newVersionContent == null || $oldVersionContent == null:
                    case $this->calculationService->determineIfMajor($newVersionContent, $oldVersionContent):
                        $major++;
                        $minor = 0;
                        $patch = 0;
                        break;

                    case $this->calculationService->determineIfMinor($newVersionContent, $oldVersionContent):
                        $minor++;
                        $patch = 0;
                        break;

                    default:
                        $patch++;
                        break;
                }
            }
        }

        $this->repository->update($newVersion, [
            'name' => $major . '.' . $minor . '.' . $patch,
        ]);
    }
}