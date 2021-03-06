<?php

namespace Tailgate\Test\Domain\Service\Group;

use Tailgate\Application\Query\Group\GroupInviteCodeQuery;
use Tailgate\Domain\Model\Group\GroupView;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Domain\Service\DataTransformer\GroupDataTransformerInterface;
use Tailgate\Domain\Service\Group\GroupInviteCodeQueryHandler;
use Tailgate\Test\BaseTestCase;

class GroupInviteCodeQueryHandlerTest extends BaseTestCase
{
    public function testItAttemptsToQueryGroupsFromGroupViewRepository()
    {
        $inviteCode = 'code';

        $groupViewRepository = $this->createMock(GroupViewRepositoryInterface::class);
        $groupViewTransformer = $this->createMock(GroupDataTransformerInterface::class);
        $groupView = $this->createMock(GroupView::class);
        $groupViewRepository->expects($this->once())
            ->method('byInviteCode')
            ->willReturn($groupView)
            ->with($inviteCode);

        $groupInviteCodeQuery = new GroupInviteCodeQuery($inviteCode);
        $groupInviteCodeQueryHandler = new GroupInviteCodeQueryHandler($groupViewRepository, $groupViewTransformer);
        $groupInviteCodeQueryHandler->handle($groupInviteCodeQuery);
    }
}
