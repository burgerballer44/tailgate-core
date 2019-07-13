<?php

namespace Tailgate\Application\Query\User;

use Tailgate\Domain\Model\User\UserViewRepositoryInterface;
use Tailgate\Application\DataTransformer\UserDataTransformerInterface;

class AllUsersQueryHandler
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

    public function handle(AllUsersQuery $allUsersQuery)
    {
        $userViews = $this->userViewRepository->all();

        $users = [];

        foreach ($userViews as $userView) {
            $users[] = $this->userViewTransformer->read($userView);
        }

        return $users;
    }
}