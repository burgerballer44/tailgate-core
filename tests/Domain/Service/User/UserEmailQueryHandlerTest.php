<?php

namespace Tailgate\Test\Domain\Service\User;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Query\User\UserEmailQuery;
use Tailgate\Domain\Service\User\UserEmailQueryHandler;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserView;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;
use Tailgate\Domain\Service\DataTransformer\UserDataTransformerInterface;

class UserEmailQueryHandlerTest extends TestCase
{
    public function testItAttemptsToGetAUserByPasswordResetTokenFromUserViewRepository()
    {
        $email = 'randomKey';

        $userViewRepository = $this->createMock(UserViewRepositoryInterface::class);
        $userViewTransformer = $this->createMock(UserDataTransformerInterface::class);
        $userView = $this->createMock(UserView::class);
        $userViewRepository->expects($this->once())
            ->method('byEmail')
            ->with($email)
            ->willReturn($userView);

        $userEmailQuery = new UserEmailQuery($email);
        $userEmailQueryHandler = new UserEmailQueryHandler($userViewRepository, $userViewTransformer);
        $userEmailQueryHandler->handle($userEmailQuery);
    }
}
