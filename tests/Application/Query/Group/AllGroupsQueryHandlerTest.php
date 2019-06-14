<?php

namespace Tailgate\Test\Application\Command\Group;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Query\Group\AllGroupsQuery;
use Tailgate\Application\Query\Group\AllGroupsQueryHandler;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;

class AllGroupsQueryHandlerTest extends TestCase
{
    public function testItAttemptsToGetAllGroupsFromUserViewRepository()
    {
        $groupViewRepository = $this->createMock(GroupViewRepositoryInterface::class);
        $groupViewRepository->expects($this->once())->method('all');

        $allGroupsQuery = new AllGroupsQuery();
        $allGroupsQueryHandler = new AllGroupsQueryHandler($groupViewRepository);
        $allGroupsQueryHandler->handle($allGroupsQuery);
    }
}
