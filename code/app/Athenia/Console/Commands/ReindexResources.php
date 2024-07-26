<?php
declare(strict_types=1);

namespace App\Athenia\Console\Commands;

use App\Athenia\Contracts\Models\CanBeIndexedContract;
use App\Athenia\Contracts\Repositories\BaseRepositoryContract;
use App\Athenia\Contracts\Repositories\ResourceRepositoryContract;
use App\Athenia\Contracts\Repositories\User\UserRepositoryContract;
use App\Athenia\Contracts\Services\Indexing\ResourceRepositoryServiceContract;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Class ReindexResources
 * @package App\Console\Commands
 */
class ReindexResources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reindex-resources';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'Reindexes all resources';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reindexes all resources in the system. May take sometime.';

    /**
     * ReindexResources constructor.
     * @param ResourceRepositoryContract $resourceRepository
     * @param ResourceRepositoryServiceContract $resourceRepositoryService
     */
    public function __construct(
        private ResourceRepositoryContract $resourceRepository,
        private ResourceRepositoryServiceContract $resourceRepositoryService,
    ) {
        parent::__construct();
    }

    /**
     * Handles reindexing everything
     */
    public function handle()
    {
        /** @var BaseRepositoryAbstract $repository */
        foreach ($this->resourceRepositoryService->getResourceRepositories() as $repository) {
            $model = $repository->getModel();

            if (!$model instanceof CanBeIndexedContract) {
                throw new RuntimeException("Please make sure your resource models implement CanBeIndexedContract.");
            }

            $tableName = $model->getTable();
            $readableName = Str::title(str_replace('_', ' ', $tableName));

            $this->line('');
            $this->info('Indexing ' . $readableName);

            $this->indexData($repository);

            $this->line('');
            $this->info('Done Indexing ' . $readableName);
        }
    }

    /**
     * Indexes all pieces of data found in this repository
     * @param BaseRepositoryContract $repository
     */
    public function indexData(BaseRepositoryContract $repository)
    {
        $models = $repository->findAll([], [], [], [],null);
        $progressBar = $this->output->createProgressBar($models->count());

        /** @var CanBeIndexedContract $model */
        foreach ($models as $model) {

            $indexedContent = $model->getContentString();

            if ($indexedContent) {
                $data = [
                    'content' => $model->getContentString(),
                ];

                if ($model->resource) {
                    $this->resourceRepository->update($model->resource, $data);
                } else {
                    $data['resource_id'] = $model->id;
                    $data['resource_type'] = $model->morphRelationName();
                    $this->resourceRepository->create($data);
                }

            } else if ($model->resource) {
                $this->resourceRepository->delete($model->resource);
            }
            $progressBar->advance();
        }
    }
}