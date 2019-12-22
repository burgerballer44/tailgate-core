<?php

namespace Tailgate\Test\Domain\Service\Group;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Query\Group\GroupQuery;
use Tailgate\Domain\Service\Group\GroupQueryHandler;
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

class GroupQueryHandlerTest extends TestCase
{
    public function testItAttemptsToGetAGroupByGroupIdFromGroupViewRepository()
    {
        $groupId = 'groupId';

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
            ->method('get')
            ->willReturn($groupView)
            ->with(
                $this->callback(function ($groupQueryGroupId) use ($groupId) {
                    return (new GroupId($groupId))->equals($groupQueryGroupId);
                })
            );
        $memberViewRepository->expects($this->once())
            ->method('getAllByGroup')
            ->willReturn($memberView)
            ->with($this->callback(function ($groupQueryGroupId) use ($groupId) {
                return (new GroupId($groupId))->equals($groupQueryGroupId);
            }));
        $playerViewRepository->expects($this->once())
            ->method('getAllByGroup')
            ->willReturn($playerView)
            ->with($this->callback(function ($groupQueryGroupId) use ($groupId) {
                return (new GroupId($groupId))->equals($groupQueryGroupId);
            }));
        $scoreViewRepository->expects($this->once())
            ->method('getAllByGroup')
            ->willReturn($scoreView)
            ->with($this->callback(function ($groupQueryGroupId) use ($groupId) {
                return (new GroupId($groupId))->equals($groupQueryGroupId);
            }));
        $followViewRepository->expects($this->once())
            ->method('getByGroup')
            ->willReturn($followView)
            ->with($this->callback(function ($groupQueryGroupId) use ($groupId) {
                return (new GroupId($groupId))->equals($groupQueryGroupId);
            }));

        $groupQuery = new GroupQuery($groupId);
        $groupQueryHandler = new GroupQueryHandler(
            $groupViewRepository,
            $memberViewRepository,
            $playerViewRepository,
            $scoreViewRepository,
            $followViewRepository,
            $groupViewTransformer
        );
        $groupQueryHandler->handle($groupQuery);
    }
}
