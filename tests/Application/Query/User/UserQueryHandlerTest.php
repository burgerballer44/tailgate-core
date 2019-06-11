<?php

namespace Tailgate\Test\Application\Command\User;

use Tailgate\Application\Query\User\UserQuery;
use Tailgate\Application\Query\User\UserQueryHandler;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;
use Tailgate\Tests\BaseTestCase;

class UserQueryHandlerTest extends BaseTestCase
{
    public function testItAttemptsToGetAUserByUserIdFromUserViewRepository()
    {
        $userId = 'idToCheck';

        $userViewRepository = $this->createMock(UserViewRepositoryInterface::class);
        $userViewRepository->expects($this->once())
            ->method('get')  
            ->with($this->callback(function($userQueryUserId) use ($userId) {
                return (new UserId($userId))->equals($userQueryUserId);
            }));

        $userQuery = new UserQuery($userId);
        $userQueryHandler = new UserQueryHandler($userViewRepository);
        $userQueryHandler->handle($userQuery);
    }
}
