<?php

namespace Tailgate\Domain\Service\User;

use Tailgate\Application\Command\User\RequestPasswordResetCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Common\Security\RandomStringInterface;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Domain\Service\AbstractService;

class RequestPasswordResetHandler extends AbstractService
{
    private $userRepository;
    private $randomStringer;

    public function __construct(
        ValidatorInterface $validator,
        UserRepositoryInterface $userRepository,
        RandomStringInterface $randomStringer
    ) {
        parent::__construct($validator);
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
