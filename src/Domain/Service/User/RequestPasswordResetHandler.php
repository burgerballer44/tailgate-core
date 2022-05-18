<?php

namespace Tailgate\Domain\Service\User;

use Tailgate\Application\Command\User\RequestPasswordResetCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\User\PasswordResetToken;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Domain\Service\Clock\Clock;
use Tailgate\Domain\Service\Validatable;
use Tailgate\Domain\Service\ValidatableService;

class RequestPasswordResetHandler implements ValidatableService
{
    use Validatable;

    private $validator;
    private $clock;
    private $userRepository;
    private $randomStringer;

    public function __construct(
        ValidatorInterface $validator,
        Clock $clock,
        UserRepositoryInterface $userRepository
    ) {
        $this->validator = $validator;
        $this->clock = $clock;
        $this->userRepository = $userRepository;
    }

    public function handle(RequestPasswordResetCommand $command)
    {
        $this->validate($command);

        $user = $this->userRepository->get(UserId::fromString($command->getUserId()));

        $user->applyPasswordResetToken(PasswordResetToken::create(), Date::fromDateTimeImmutable($this->clock->currentTime()));

        $this->userRepository->add($user);

        return [
            'userId' => $user->getUserId(),
            'passwordResetToken' => $user->getPasswordResetToken(),
        ];
    }
}
