<?php

namespace Tailgate\Application\Query\User;

use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;
use Tailgate\Application\DataTransformer\UserDataTransformerInterface;

class UserQueryHandler
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

    public function handle(UserQuery $userQuery)
    {
        $userView = $this->userViewRepository->get(UserId::fromString($userQuery->getUserId()));
        return $this->userViewTransformer->read($userView);
    }
}