<?php
declare(strict_types=1);

namespace App\Listeners\User;

use App\Contracts\Repositories\Messaging\MessageRepositoryContract;
use App\Events\User\ForgotPasswordEvent;

/**
 * Class ForgotPasswordListener
 * @package App\Listeners\User
 */
class ForgotPasswordListener
{
    /**
     * @var MessageRepositoryContract
     */
    private $messageRepository;

    /**
     * ForgotPasswordListener constructor.
     * @param MessageRepositoryContract $messageRepository
     */
    public function __construct(MessageRepositoryContract $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    /**
     * Sends the forgot password email to a user
     *
     * @param ForgotPasswordEvent $event
     */
    public function handle(ForgotPasswordEvent $event)
    {
        $passwordToken = $event->getPasswordToken();

        $this->messageRepository->create([
            'subject' => 'Reset Password Request',
            'template' => 'forgot-password',
            'email' => $passwordToken->user->email,
            'data' => [
                'greeting' => 'Hello ' . $passwordToken->user->first_name . ',',
                'token' => $passwordToken->token,
            ],
        ], $passwordToken->user);
    }
}