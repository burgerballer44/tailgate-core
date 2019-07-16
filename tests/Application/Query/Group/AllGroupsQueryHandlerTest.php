<?php

namespace Tailgate\Test\Application\Command\Group;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Query\Group\AllGroupsQuery;
use Tailgate\Application\Query\Group\AllGroupsQueryHandler;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Application\DataTransformer\GroupDataTransformerInterface;

class AllGroupsQueryHandlerTest extends TestCase
{
    public function testItAttemptsToGetAllGroupsFromUserViewRepository()
    {
        $groupViewRepository = $this->createMock(GroupViewRepositoryInterface::class);
        $groupViewTransformer = $this->createMock(GroupDataTransformerInterface::class);
        $groupViewRepository->expects($this->once())->method('all')->willReturn([]);

        $allGroupsQuery = new AllGroupsQuery();
        $allGroupsQueryHandler = new AllGroupsQueryHandler($groupViewRepository, $groupViewTransformer);
        $allGroupsQueryHandler->handle($allGroupsQuery);
    }
}
