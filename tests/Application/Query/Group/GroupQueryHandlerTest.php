<?php

namespace Tailgate\Test\Application\Command\Group;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Query\Group\GroupQuery;
use Tailgate\Application\Query\Group\GroupQueryHandler;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;

class GroupQueryHandlerTest extends TestCase
{
    public function testItAttemptsToGetAGroupByGroupIdFromGroupViewRepository()
    {
        $groupId = 'groupId';

        $groupViewRepository = $this->createMock(GroupViewRepositoryInterface::class);
        $groupViewRepository->expects($this->once())
            ->method('get')  
            ->with($this->callback(function($groupQueryGroupId) use ($groupId) {
                return (new GroupId($groupId))->equals($groupQueryGroupId);
            }));

        $groupQuery = new GroupQuery($groupId);
        $groupQueryHandler = new GroupQueryHandler($groupViewRepository);
        $groupQueryHandler->handle($groupQuery);
    }
}
