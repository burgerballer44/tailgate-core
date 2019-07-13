<?php

namespace Tailgate\Test\Application\Command\User;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Query\User\AllUsersQuery;
use Tailgate\Application\Query\User\AllUsersQueryHandler;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;
use Tailgate\Application\DataTransformer\UserDataTransformerInterface;

class AllUsersQueryHandlerTest extends TestCase
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
