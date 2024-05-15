<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Vote;

use App\Athenia\Contracts\Repositories\Vote\BallotItemRepositoryContract;
use App\Athenia\Contracts\Repositories\Vote\BallotRepositoryContract;
use App\Athenia\Models\BaseModelAbstract;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use App\Athenia\Traits\CanGetAndUnset;
use App\Models\Vote\Ballot;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class BallotRepository
 * @package App\Repositories\Vote
 */
class BallotRepository extends BaseRepositoryAbstract implements BallotRepositoryContract
{
    use CanGetAndUnset;

    /**
     * @var BallotItemRepositoryContract
     */
    private $ballotSubjectRepository;

    /**
     * BallotRepository constructor.
     * @param Ballot $model
     * @param LogContract $log
     * @param BallotItemRepositoryContract $ballotSubjectRepository
     */
    public function __construct(Ballot $model, LogContract $log,
                                BallotItemRepositoryContract $ballotSubjectRepository)
    {
        parent::__construct($model, $log);
        $this->ballotSubjectRepository = $ballotSubjectRepository;
    }

    /**
     * overrides the parent in order to create all related ballot subjects
     *
     * @param array $data
     * @param BaseModelAbstract|null $relatedModel
     * @param array $forcedValues
     * @return BaseModelAbstract
     */
    public function create(array $data = [], BaseModelAbstract $relatedModel = null, array $forcedValues = [])
    {
        $ballotItems = $this->getAndUnset($data, 'ballot_items', []);
        $model = parent::create($data, $relatedModel, $forcedValues);

        $this->syncChildModels($this->ballotSubjectRepository, $model, $ballotItems);

        return $model;
    }

    /**
     * Makes sure to sync child models properly
     *
     * @param Ballot|BaseModelAbstract $model
     * @param array $data
     * @param array $forcedValues
     * @return BaseModelAbstract
     */
    public function update(BaseModelAbstract $model, array $data, array $forcedValues = []): BaseModelAbstract
    {
        $ballotItems = $this->getAndUnset($data, 'ballot_items', null);

        if ($ballotItems !== null) {
            $this->syncChildModels($this->ballotSubjectRepository, $model, $ballotItems, $model->ballotItems);
        }

        return parent::update($model, $data, $forcedValues);
    }
}
