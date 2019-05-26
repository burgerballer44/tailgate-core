<?php

namespace Tailgate\Test\Application\Command\User;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Query\User\AllUsersQuery;
use Tailgate\Application\Query\User\AllUsersQueryHandler;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;

class AllUsersQueryHandlerTest extends TestCase
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
