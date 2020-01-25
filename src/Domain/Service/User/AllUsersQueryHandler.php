<?php

namespace Tailgate\Domain\Service\User;

use Tailgate\Application\Query\User\AllUsersQuery;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;
use Tailgate\Domain\Service\DataTransformer\UserDataTransformerInterface;

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

    public function handle()
    {
        $userViews = $this->userViewRepository->all();

        $users = [];

        foreach ($userViews as $userView) {
            $users[] = $this->userViewTransformer->read($userView);
        }

        return $users;
    }
}
