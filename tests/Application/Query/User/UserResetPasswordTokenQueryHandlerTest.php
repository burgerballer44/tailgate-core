<?php

namespace Tailgate\Test\Application\Command\User;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Query\User\UserResetPasswordTokenQuery;
use Tailgate\Application\Query\User\UserResetPasswordTokenQueryHandler;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserView;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;
use Tailgate\Application\DataTransformer\UserDataTransformerInterface;

class UserResetPasswordTokenQueryHandlerTest extends TestCase
{
    public function testItAttemptsToGetAUserByPasswordResetTokenFromUserViewRepository()
    {
        $passwordResetToken = 'randomKey';

        $userViewRepository = $this->createMock(UserViewRepositoryInterface::class);
        $userViewTransformer = $this->createMock(UserDataTransformerInterface::class);
        $userView = $this->createMock(UserView::class);
        $userViewRepository->expects($this->once())
            ->method('byPasswordResetToken')
            ->with($passwordResetToken)
            ->willReturn($userView);

        $userResetPasswordTokenQuery = new UserResetPasswordTokenQuery($passwordResetToken);
        $userResetPasswordTokenQueryHandler = new UserResetPasswordTokenQueryHandler($userViewRepository, $userViewTransformer);
        $userResetPasswordTokenQueryHandler->handle($userResetPasswordTokenQuery);
    }
}
