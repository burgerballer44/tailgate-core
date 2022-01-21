<?php

namespace Tailgate\Test\Domain\Service\User;

use Tailgate\Test\BaseTestCase;
use Tailgate\Application\Query\User\UserResetPasswordTokenQuery;
use Tailgate\Domain\Service\User\UserResetPasswordTokenQueryHandler;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserView;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;
use Tailgate\Domain\Service\DataTransformer\UserDataTransformerInterface;

class UserResetPasswordTokenQueryHandlerTest extends BaseTestCase
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
