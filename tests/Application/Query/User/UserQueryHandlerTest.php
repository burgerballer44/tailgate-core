<?php

namespace Tailgate\Test\Application\Command\User;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Query\User\UserQuery;
use Tailgate\Application\Query\User\UserQueryHandler;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserView;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;
use Tailgate\Application\DataTransformer\UserDataTransformerInterface;

class UserQueryHandlerTest extends TestCase
{
    public function testItAttemptsToGetAUserByUserIdFromUserViewRepository()
    {
        $userId = 'userId';

        $userViewRepository = $this->createMock(UserViewRepositoryInterface::class);
        $userViewTransformer = $this->createMock(UserDataTransformerInterface::class);
        $userView = $this->createMock(UserView::class);
        $userViewRepository->expects($this->once())
            ->method('get')
            ->with($this->callback(function ($userQueryUserId) use ($userId) {
                return (new UserId($userId))->equals($userQueryUserId);
            }))
            ->willReturn($userView);

        $userQuery = new UserQuery($userId);
        $userQueryHandler = new UserQueryHandler($userViewRepository, $userViewTransformer);
        $userQueryHandler->handle($userQuery);
    }
}
