<?php

namespace Tailgate\Test\Application\Command\Group;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Query\Group\GroupQuery;
use Tailgate\Application\Query\Group\GroupQueryHandler;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupView;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Domain\Model\Group\MemberViewRepositoryInterface;
use Tailgate\Domain\Model\Group\ScoreViewRepositoryInterface;
use Tailgate\Application\DataTransformer\GroupDataTransformerInterface;

class GroupQueryHandlerTest extends TestCase
{
    public function testItAttemptsToGetAGroupByGroupIdFromGroupViewRepository()
    {
        $groupId = 'groupId';

        $groupViewRepository = $this->createMock(GroupViewRepositoryInterface::class);
        $memberViewRepository = $this->createMock(MemberViewRepositoryInterface::class);
        $scoreViewRepository = $this->createMock(ScoreViewRepositoryInterface::class);
        $groupViewTransformer = $this->createMock(GroupDataTransformerInterface::class);
        $groupView = $this->createMock(GroupView::class);
        $groupViewRepository->expects($this->once())
            ->method('get')
            ->willReturn($groupView)
            ->with($this->callback(function($groupQueryGroupId) use ($groupId) {
                return (new GroupId($groupId))->equals($groupQueryGroupId);
            }));
        $memberViewRepository->expects($this->once())
            ->method('getAllByGroup')
            ->willReturn($groupView)
            ->with($this->callback(function($groupQueryGroupId) use ($groupId) {
                return (new GroupId($groupId))->equals($groupQueryGroupId);
            }));
        $scoreViewRepository->expects($this->once())
            ->method('getAllByGroup')
            ->willReturn($groupView)
            ->with($this->callback(function($groupQueryGroupId) use ($groupId) {
                return (new GroupId($groupId))->equals($groupQueryGroupId);
            }));

        $groupQuery = new GroupQuery($groupId);
        $groupQueryHandler = new GroupQueryHandler(
            $groupViewRepository,
            $memberViewRepository,
            $scoreViewRepository,
            $groupViewTransformer
        );
        $groupQueryHandler->handle($groupQuery);
    }
}
