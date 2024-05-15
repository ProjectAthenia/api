<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Payment;

use App\Athenia\Contracts\Repositories\Payment\PaymentMethodRepositoryContract;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use App\Models\Payment\PaymentMethod;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class PaymentMethodRepository
 * @package App\Repositories\Payment
 */
class PaymentMethodRepository extends BaseRepositoryAbstract implements PaymentMethodRepositoryContract
{
    /**
     * PaymentMethodRepository constructor.
     * @param PaymentMethod $model
     * @param LogContract $log
     */
    public function __construct(PaymentMethod $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}