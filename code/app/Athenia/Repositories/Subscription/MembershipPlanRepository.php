<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Subscription;

use App\Athenia\Contracts\Repositories\Subscription\MembershipPlanRateRepositoryContract;
use App\Athenia\Contracts\Repositories\Subscription\MembershipPlanRepositoryContract;
use App\Athenia\Models\BaseModelAbstract;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use App\Athenia\Traits\CanGetAndUnset;
use App\Models\Subscription\MembershipPlan;
use Illuminate\Database\Eloquent\Model;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class MembershipPlanRepository
 * @package App\Repositories\Subscription
 */
class MembershipPlanRepository extends BaseRepositoryAbstract implements MembershipPlanRepositoryContract
{
    use CanGetAndUnset;

    /**
     * @var MembershipPlanRateRepositoryContract
     */
    private MembershipPlanRateRepositoryContract $membershipPlanRateRepository;

    /**
     * MembershipPlanRepository constructor.
     * @param MembershipPlan $model
     * @param LogContract $log
     * @param MembershipPlanRateRepositoryContract $membershipPlanRateRepository
     */
    public function __construct(MembershipPlan $model, LogContract $log,
                                MembershipPlanRateRepositoryContract $membershipPlanRateRepository)
    {
        parent::__construct($model, $log);
        $this->membershipPlanRateRepository = $membershipPlanRateRepository;
    }

    /**
     * Overrides the create in order to create the current rate
     *
     * @param array $data
     * @param BaseModelAbstract|null $relatedModel
     * @param array $forcedValues
     * @return BaseModelAbstract
     */
    public function create(array $data = [], BaseModelAbstract $relatedModel = null, array $forcedValues = [])
    {
        $cost = $this->getAndUnset($data, 'current_cost');
        $features = $this->getAndUnset($data, 'features', []);

        /** @var MembershipPlan $model */
        $model = parent::create($data, $relatedModel, $forcedValues);

        if ($cost) {
            $this->membershipPlanRateRepository->create([
                'cost' => $cost,
                'active' => true,
            ], $model);
        }
        $model->features()->sync($features);

        return $model;
    }

    /**
     * @param MembershipPlan|BaseModelAbstract $model
     * @param array $data
     * @param array $forcedValues
     * @return BaseModelAbstract
     */
    public function update(BaseModelAbstract $model, array $data, array $forcedValues = []): BaseModelAbstract
    {
        $cost = $this->getAndUnset($data, 'current_cost');
        $features = $this->getAndUnset($data, 'features', null);

        if ($cost && $cost != $model->current_cost) {

            foreach ($model->membershipPlanRates as $membershipPlanRate) {
                $this->membershipPlanRateRepository->update($membershipPlanRate, [
                    'active' => false,
                ]);
            }

            $this->membershipPlanRateRepository->create([
                'cost' => $cost,
                'active' => true,
            ], $model);

            $model->unsetRelations();
        }

        if ($features) {
            $model->features()->sync($features);
        }

        return parent::update($model, $data, $forcedValues);
    }

    /**
     * Finds the default membership plan that will be applied to an entity if the entity is not subscribed
     *
     * @param string $entityType
     * @return MembershipPlan|Model|null
     */
    public function findDefaultMembershipPlanForEntity(string $entityType): ?MembershipPlan
    {
        return $this->model->newQuery()
            ->where('entity_type', '=', $entityType)
            ->where('default', '=', 1)
            ->first();
    }
}
