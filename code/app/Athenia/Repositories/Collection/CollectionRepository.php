<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Collection;

use App\Athenia\Contracts\Repositories\Collection\CollectionItemRepositoryContract;
use App\Athenia\Contracts\Repositories\Collection\CollectionRepositoryContract;
use App\Athenia\Models\BaseModelAbstract;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use App\Athenia\Traits\CanGetAndUnset;
use App\Models\Collection\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Psr\Log\LoggerInterface as LogContract;

class CollectionRepository extends BaseRepositoryAbstract implements CollectionRepositoryContract
{
    use CanGetAndUnset;

    /**
     * @param Collection $model
     * @param LogContract $log
     * @param CollectionItemRepositoryContract $collectionItemRepository
     */
    public function __construct(Collection $model, LogContract $log,
                                private CollectionItemRepositoryContract $collectionItemRepository)
    {
        parent::__construct($model, $log);
    }

    /**
     * @param BaseModelAbstract $model
     * @param array $data
     * @param array $forcedValues
     * @return BaseModelAbstract
     */
    public function update(BaseModelAbstract $model, array $data, array $forcedValues = []): BaseModelAbstract
    {
        $newOrder = $this->getAndUnset($data, 'collection_item_order');

        /** @var Collection $updated */
        $updated = parent::update($model, $data, $forcedValues);

        if (is_array($newOrder)) {
            foreach ($newOrder as $index => $id) {
                try {
                    $collectionItem = $this->collectionItemRepository->findOrFail($id);
                    $this->collectionItemRepository->update($collectionItem, [
                        'order' => $index,
                    ]);
                } catch (ModelNotFoundException $e) {}
            }

            // Load this so that it is returned in reqeusts
            $updated->collectionItems;
        }

        return $updated;
    }
}