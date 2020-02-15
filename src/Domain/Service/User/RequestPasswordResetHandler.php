<?php

namespace Tailgate\Domain\Service\User;

use Tailgate\Application\Command\User\RequestPasswordResetCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Domain\Service\Security\RandomStringInterface;
use Tailgate\Domain\Service\Validatable;
use Tailgate\Domain\Service\ValidatableService;

class RequestPasswordResetHandler implements ValidatableService
{
    use Validatable;
    
    private $validator;
    private $userRepository;
    private $randomStringer;

    public function __construct(
        ValidatorInterface $validator,
        UserRepositoryInterface $userRepository,
        RandomStringInterface $randomStringer
    ) {
        $this->validator = $validator;
        $this->userRepository = $userRepository;
        $this->randomStringer = $randomStringer;
    }

    public function handle(RequestPasswordResetCommand $command)
    {
        $this->validate($command);

        $userId = $command->getUserId();

        $user = $this->userRepository->get(UserId::fromString($userId));

        $user->createPasswordResetToken($this->randomStringer->generate());

        $this->userRepository->add($user);

        return [
            'userId'             => $user->getId(),
            'passwordResetToken' => $user->getPasswordResetToken()
        ];
    }
}
