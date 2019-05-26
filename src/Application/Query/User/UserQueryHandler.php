<?php

namespace Tailgate\Application\Query\User;

use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;

class UserQueryHandler
{
    private $userViewRepository;

    public function __construct(UserViewRepositoryInterface $userViewRepository)
    {
        $this->userViewRepository = $userViewRepository;
    }

    public function handle(UserQuery $userQuery)
    {
        return $this->userViewRepository->get(new UserId($userQuery->getUserId()));
    }
}