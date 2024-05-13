<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\Models\CanBeIndexedContract;
use App\Contracts\Repositories\BaseRepositoryContract;
use App\Contracts\Repositories\ResourceRepositoryContract;
use App\Contracts\Repositories\User\UserRepositoryContract;
use Illuminate\Console\Command;

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
     * @var ResourceRepositoryContract
     */
    private $resourceRepository;

    /**
     * @var UserRepositoryContract
     */
    private $userRepository;

    /**
     * ReindexResources constructor.
     * @param ResourceRepositoryContract $resourceRepository
     * @param UserRepositoryContract $userRepository
     */
    public function __construct(ResourceRepositoryContract $resourceRepository,
                                UserRepositoryContract $userRepository)
    {
        parent::__construct();
        $this->resourceRepository = $resourceRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Handles reindexing everything
     */
    public function handle()
    {
        $this->line('');
        $this->info('Indexing Users');

        $this->indexData($this->userRepository);

        $this->line('');
        $this->info('Done Indexing Users');
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

            $progressBar->advance();
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
        }
    }
}