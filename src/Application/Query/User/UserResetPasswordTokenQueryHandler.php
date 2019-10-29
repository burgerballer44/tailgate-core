<?php

namespace Tailgate\Application\Query\User;

use Tailgate\Domain\Model\User\UserViewRepositoryInterface;
use Tailgate\Application\DataTransformer\UserDataTransformerInterface;

class UserResetPasswordTokenQueryHandler
{
    private $userViewRepository;
    private $userViewTransformer;

    public function __construct(
        UserViewRepositoryInterface $userViewRepository,
        UserDataTransformerInterface $userViewTransformer
    ) {
        $this->userViewRepository = $userViewRepository;
        $this->userViewTransformer = $userViewTransformer;
    }

    public function handle(UserResetPasswordTokenQuery $query)
    {
        $passwordResetToken = $query->getPasswordResetToken();

        $userView = $this->userViewRepository->byPasswordResetToken($passwordResetToken);
        return $this->userViewTransformer->read($userView);
    }
}
