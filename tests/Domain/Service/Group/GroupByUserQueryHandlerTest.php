<?php

namespace Tailgate\Test\Domain\Service\Group;

use Tailgate\Test\BaseTestCase;
use Tailgate\Application\Query\Group\GroupByUserQuery;
use Tailgate\Domain\Service\Group\GroupByUserQueryHandler;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupView;
use Tailgate\Domain\Model\Group\FollowView;
use Tailgate\Domain\Model\Group\MemberView;
use Tailgate\Domain\Model\Group\PlayerView;
use Tailgate\Domain\Model\Group\ScoreView;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Domain\Model\Group\FollowViewRepositoryInterface;
use Tailgate\Domain\Model\Group\MemberViewRepositoryInterface;
use Tailgate\Domain\Model\Group\PlayerViewRepositoryInterface;
use Tailgate\Domain\Model\Group\ScoreViewRepositoryInterface;
use Tailgate\Domain\Service\DataTransformer\GroupDataTransformerInterface;

class GroupByUserQueryHandlerTest extends BaseTestCase
{
    public function testItAttemptsToGetAGroupByGroupIdFromGroupViewRepository()
    {
        $groupId = 'groupId';
        $userId = 'userId';

        $groupViewRepository = $this->createMock(GroupViewRepositoryInterface::class);
        $memberViewRepository = $this->createMock(MemberViewRepositoryInterface::class);
        $followViewRepository = $this->createMock(FollowViewRepositoryInterface::class);
        $playerViewRepository = $this->createMock(PlayerViewRepositoryInterface::class);
        $scoreViewRepository = $this->createMock(ScoreViewRepositoryInterface::class);
        $groupViewTransformer = $this->createMock(GroupDataTransformerInterface::class);
        $groupView = $this->createMock(GroupView::class);
        $followView = $this->createMock(FollowView::class);
        $memberView = $this->createMock(MemberView::class);
        $playerView = $this->createMock(PlayerView::class);
        $scoreView = $this->createMock(ScoreView::class);
        $groupViewRepository->expects($this->once())
            ->method('getByUser')
            ->willReturn($groupView)
            ->with(
                $this->callback(function ($userQueryUserId) use ($userId) {
                    return (new UserId($userId))->equals($userQueryUserId);
                }),
                $this->callback(function ($groupByUserQueryGroupId) use ($groupId) {
                    return (new GroupId($groupId))->equals($groupByUserQueryGroupId);
                })
            );
        $memberViewRepository->expects($this->once())
            ->method('getAllByGroup')
            ->willReturn($memberView)
            ->with($this->callback(function ($groupByUserQueryGroupId) use ($groupId) {
                return (new GroupId($groupId))->equals($groupByUserQueryGroupId);
            }));
        $playerViewRepository->expects($this->once())
            ->method('getAllByGroup')
            ->willReturn($playerView)
            ->with($this->callback(function ($groupByUserQueryGroupId) use ($groupId) {
                return (new GroupId($groupId))->equals($groupByUserQueryGroupId);
            }));
        $scoreViewRepository->expects($this->once())
            ->method('getAllByGroup')
            ->willReturn($scoreView)
            ->with($this->callback(function ($groupByUserQueryGroupId) use ($groupId) {
                return (new GroupId($groupId))->equals($groupByUserQueryGroupId);
            }));
        $followViewRepository->expects($this->once())
            ->method('getByGroup')
            ->willReturn($followView)
            ->with($this->callback(function ($groupByUserQueryGroupId) use ($groupId) {
                return (new GroupId($groupId))->equals($groupByUserQueryGroupId);
            }));

        $groupByUserQuery = new GroupByUserQuery($userId, $groupId);
        $groupByUserQueryHandler = new GroupByUserQueryHandler(
            $groupViewRepository,
            $memberViewRepository,
            $playerViewRepository,
            $scoreViewRepository,
            $followViewRepository,
            $groupViewTransformer
        );
        $groupByUserQueryHandler->handle($groupByUserQuery);
    }
}
