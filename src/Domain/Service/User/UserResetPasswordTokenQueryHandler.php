<?php

namespace Tailgate\Domain\Service\User;

use Tailgate\Application\Query\User\UserResetPasswordTokenQuery;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;
use Tailgate\Domain\Service\DataTransformer\UserDataTransformerInterface;

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
        $userView = $this->userViewRepository->byPasswordResetToken($query->getPasswordResetToken());

        return $this->userViewTransformer->read($userView);
    }
}
