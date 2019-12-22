<?php

namespace Tailgate\Domain\Service\User;

use Tailgate\Application\Query\User\UserEmailQuery;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;
use Tailgate\Domain\Service\DataTransformer\UserDataTransformerInterface;

class UserEmailQueryHandler
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

    public function handle(UserEmailQuery $query)
    {
        $email = $query->getEmail();

        $userView = $this->userViewRepository->byEmail($email);
        return $this->userViewTransformer->read($userView);
    }
}
