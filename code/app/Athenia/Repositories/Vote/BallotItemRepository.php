<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Vote;

use App\Athenia\Contracts\Repositories\Vote\BallotItemOptionRepositoryContract;
use App\Athenia\Contracts\Repositories\Vote\BallotItemRepositoryContract;
use App\Athenia\Models\BaseModelAbstract;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use App\Athenia\Traits\CanGetAndUnset;
use App\Models\Vote\BallotItem;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class BallotItemRepository
 * @package App\Repositories\Vote
 */
class BallotItemRepository extends BaseRepositoryAbstract implements BallotItemRepositoryContract
{
    use CanGetAndUnset;

    /**
     * @var BallotItemOptionRepositoryContract
     */
    private BallotItemOptionRepositoryContract $ballotItemOptionRepository;

    /**
     * BallotSubjectRepository constructor.
     * @param BallotItem $model
     * @param LogContract $log
     * @param BallotItemOptionRepositoryContract $ballotItemOptionRepository
     */
    public function __construct(BallotItem $model, LogContract $log,
                                BallotItemOptionRepositoryContract $ballotItemOptionRepository)
    {
        parent::__construct($model, $log);
        $this->ballotItemOptionRepository = $ballotItemOptionRepository;
    }

    /**
     * Overrides the parent create to syn related models
     *
     * @param array $data
     * @param BaseModelAbstract|null $relatedModel
     * @param array $forcedValues
     * @return BaseModelAbstract
     */
    public function create(array $data = [], BaseModelAbstract $relatedModel = null, array $forcedValues = [])
    {
        $ballotItemOptions = $this->getAndUnset($data, 'ballot_item_options', []);
        $ballotItem = parent::create($data, $relatedModel, $forcedValues);

        $this->syncChildModels($this->ballotItemOptionRepository, $ballotItem, $ballotItemOptions);

        return $ballotItem;
    }

    /**
     * Makes sure to sync child models properly
     *
     * @param BallotItem|BaseModelAbstract $model
     * @param array $data
     * @param array $forcedValues
     * @return BaseModelAbstract
     */
    public function update(BaseModelAbstract $model, array $data, array $forcedValues = []): BaseModelAbstract
    {
        $ballotItemOptions = $this->getAndUnset($data, 'ballot_item_options', null);

        if ($ballotItemOptions !== null) {
            $this->syncChildModels($this->ballotItemOptionRepository, $model, $ballotItemOptions, $model->ballotItemOptions);
        }

        return parent::update($model, $data, $forcedValues);
    }
}
