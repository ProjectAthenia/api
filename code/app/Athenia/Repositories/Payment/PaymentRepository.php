<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Payment;

use App\Athenia\Contracts\Repositories\Payment\LineItemRepositoryContract;
use App\Athenia\Contracts\Repositories\Payment\PaymentRepositoryContract;
use App\Athenia\Models\BaseModelAbstract;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use App\Athenia\Traits\CanGetAndUnset;
use App\Models\Payment\Payment;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class PaymentRepository
 * @package App\Repositories\Payment
 */
class PaymentRepository extends BaseRepositoryAbstract implements PaymentRepositoryContract
{
    use CanGetAndUnset;

    /**
     * @var LineItemRepositoryContract
     */
    private $lineItemRepository;

    /**
     * PaymentRepository constructor.
     * @param Payment $model
     * @param LogContract $log
     */
    public function __construct(Payment $model, LogContract $log,
                                LineItemRepositoryContract $lineItemRepository)
    {
        parent::__construct($model, $log);
        $this->lineItemRepository = $lineItemRepository;
    }

    /**
     * Overrides the parent in order to sync the line items
     *
     * @param array $data
     * @param BaseModelAbstract|null $relatedModel
     * @param array $forcedValues
     * @return BaseModelAbstract
     */
    public function create(array $data = [], BaseModelAbstract $relatedModel = null, array $forcedValues = [])
    {
        $lineItems = $this->getAndUnset($data, 'line_items', []);
        $model = parent::create($data, $relatedModel, $forcedValues);

        $this->syncChildModels($this->lineItemRepository, $model, $lineItems);

        return $model;
    }

    /**
     * Overrides the parent in order to sync the line items
     *
     * @param BaseModelAbstract $model
     * @param array $data
     * @param array $forcedValues
     * @return BaseModelAbstract
     */
    public function update(BaseModelAbstract $model, array $data, array $forcedValues = []): BaseModelAbstract
    {
        $lineItems = $this->getAndUnset($data, 'line_items', null);
        /** @var Payment $updated */
        $updated = parent::update($model, $data, $forcedValues);

        if ($lineItems) {
            $this->syncChildModels($this->lineItemRepository, $model, $lineItems, $updated->lineItems);
        }

        return $updated;
    }
}