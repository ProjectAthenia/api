<?php
declare(strict_types=1);

namespace App\Athenia\Providers;

use App\Athenia\Validators\ArticleVersion\SelectedIterationBelongsToArticleValidator;
use App\Athenia\Validators\ForgotPassword\TokenIsNotExpiredValidator;
use App\Athenia\Validators\ForgotPassword\UserOwnsTokenValidator;
use App\Athenia\Validators\NotPresentValidator;
use App\Athenia\Validators\OwnedByValidator;
use App\Athenia\Validators\Subscription\MembershipPlanRateIsActiveValidator;
use App\Athenia\Validators\Subscription\PaymentMethodIsOwnedByEntityValidator;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Support\ServiceProvider;

/**
 * Class AppValidatorProvider
 * @package App\Providers
 */
abstract class BaseValidatorProvider extends ServiceProvider
{
    /**
     * Registers all application validators
     */
    public function boot(): void
    {
        /** @var Factory $validator */
        $validator = $this->app->make(Factory::class);

        $validator->extend('token_is_not_expired', TokenIsNotExpiredValidator::class);
        $validator->extend('user_owns_token', UserOwnsTokenValidator::class);
        $validator->extend('not_present', NotPresentValidator::class);
        $validator->extend(MembershipPlanRateIsActiveValidator::KEY, MembershipPlanRateIsActiveValidator::class);
        $validator->extend(OwnedByValidator::KEY, OwnedByValidator::class);
        $validator->extend(PaymentMethodIsOwnedByEntityValidator::KEY, PaymentMethodIsOwnedByEntityValidator::class);
        $validator->extend(SelectedIterationBelongsToArticleValidator::KEY, SelectedIterationBelongsToArticleValidator::class);
    }

    /**
     * Register any of your application specific validators here
     *
     * @param Factory $validatorFactory
     * @return void
     */
    public abstract function registerValidators(Factory $validatorFactory): void;
}