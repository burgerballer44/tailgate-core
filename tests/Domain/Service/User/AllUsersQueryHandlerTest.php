<?php

namespace Tailgate\Test\Domain\Service\User;

use Tailgate\Application\Query\User\AllUsersQuery;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;
use Tailgate\Domain\Service\DataTransformer\UserDataTransformerInterface;
use Tailgate\Domain\Service\User\AllUsersQueryHandler;
use Tailgate\Test\BaseTestCase;

class AllUsersQueryHandlerTest extends BaseTestCase
{
    public function testItAttemptsToGetAllUsersFromUserViewRepository()
    {
        $userViewRepository = $this->createMock(UserViewRepositoryInterface::class);
        $userViewTransformer = $this->createMock(UserDataTransformerInterface::class);
        $userViewRepository->expects($this->once())->method('all')->willReturn([]);

        $allUsersQuery = new AllUsersQuery();
        $allUsersQueryHandler = new AllUsersQueryHandler($userViewRepository, $userViewTransformer);
        $allUsersQueryHandler->handle($allUsersQuery);
    }
}
