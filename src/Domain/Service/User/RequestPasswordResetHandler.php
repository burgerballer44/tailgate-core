<?php

namespace Tailgate\Domain\Service\User;

use Tailgate\Application\Command\User\RequestPasswordResetCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\User\PasswordResetToken;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Domain\Service\Clock\Clock;

class RequestPasswordResetHandler
{
    private $clock;
    private $userRepository;
    private $randomStringer;

    public function __construct(
        Clock $clock,
        UserRepositoryInterface $userRepository
    ) {
        $this->clock = $clock;
        $this->userRepository = $userRepository;
    }

    public function handle(RequestPasswordResetCommand $command)
    {
        $user = $this->userRepository->get(UserId::fromString($command->getUserId()));

        $user->applyPasswordResetToken(PasswordResetToken::create(), Date::fromDateTimeImmutable($this->clock->currentTime()));

        $this->userRepository->add($user);

        return [
            'userId' => $user->getUserId(),
            'passwordResetToken' => $user->getPasswordResetToken(),
        ];
    }
}
