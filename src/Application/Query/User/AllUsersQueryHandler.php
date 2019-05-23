<?php

namespace Tailgate\Application\Query\User;

use Tailgate\Domain\Model\User\UserViewRepositoryInterface;

class AllUsersQueryHandler
{
    private $userViewRepository;

    public function __construct(UserViewRepositoryInterface $userViewRepository)
    {
        $this->userViewRepository = $userViewRepository;
    }

    public function handle(AllUsersQuery $allUsersQuery)
    {
        return $this->userViewRepository->all();
    }
}