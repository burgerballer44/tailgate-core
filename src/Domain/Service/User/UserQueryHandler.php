<?php

namespace Tailgate\Domain\Service\User;

use Tailgate\Application\Query\User\UserQuery;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;
use Tailgate\Domain\Service\DataTransformer\UserDataTransformerInterface;

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

    public function handle(UserQuery $query)
    {
        $userView = $this->userViewRepository->get(UserId::fromString($query->getUserId()));
        return $this->userViewTransformer->read($userView);
    }
}
