<?php

namespace Tailgate\Test\Application\Command\Group;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Query\Group\GroupQuery;
use Tailgate\Application\Query\Group\GroupQueryHandler;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupView;
use Tailgate\Domain\Model\Group\MemberView;
use Tailgate\Domain\Model\Group\PlayerView;
use Tailgate\Domain\Model\Group\ScoreView;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Domain\Model\Group\MemberViewRepositoryInterface;
use Tailgate\Domain\Model\Group\PlayerViewRepositoryInterface;
use Tailgate\Domain\Model\Group\ScoreViewRepositoryInterface;
use Tailgate\Application\DataTransformer\GroupDataTransformerInterface;

class GroupQueryHandlerTest extends TestCase
{
    public function testItAttemptsToGetAGroupByGroupIdFromGroupViewRepository()
    {
        $groupId = 'groupId';
        $userId = 'userId';

        $groupViewRepository = $this->createMock(GroupViewRepositoryInterface::class);
        $memberViewRepository = $this->createMock(MemberViewRepositoryInterface::class);
        $playerViewRepository = $this->createMock(PlayerViewRepositoryInterface::class);
        $scoreViewRepository = $this->createMock(ScoreViewRepositoryInterface::class);
        $groupViewTransformer = $this->createMock(GroupDataTransformerInterface::class);
        $groupView = $this->createMock(GroupView::class);
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

        $groupQuery = new GroupQuery($userId, $groupId);
        $groupQueryHandler = new GroupQueryHandler(
            $groupViewRepository,
            $memberViewRepository,
            $playerViewRepository,
            $scoreViewRepository,
            $groupViewTransformer
        );
        $groupQueryHandler->handle($groupQuery);
    }
}
