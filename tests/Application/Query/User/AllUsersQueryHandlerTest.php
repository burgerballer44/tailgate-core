<?php

namespace Tailgate\Test\Application\Command\User;

use Tailgate\Application\Query\User\AllUsersQuery;
use Tailgate\Application\Query\User\AllUsersQueryHandler;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;
use Tailgate\Tests\BaseTestCase;

class AllUsersQueryHandlerTest extends BaseTestCase
{
    public function testItAttemptsToGetAllUsersFromUserViewRepository()
    {
        $userViewRepository = $this->createMock(UserViewRepositoryInterface::class);
        $userViewRepository->expects($this->once())->method('all');

        $allUsersQuery = new AllUsersQuery();
        $allUsersQueryHandler = new AllUsersQueryHandler($userViewRepository);
        $allUsersQueryHandler->handle($allUsersQuery);
    }
}
