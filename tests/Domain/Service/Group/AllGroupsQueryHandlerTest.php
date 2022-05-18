<?php

namespace Tailgate\Test\Domain\Service\Group;

use Tailgate\Application\Query\Group\AllGroupsQuery;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Domain\Service\DataTransformer\GroupDataTransformerInterface;
use Tailgate\Domain\Service\Group\AllGroupsQueryHandler;
use Tailgate\Test\BaseTestCase;

class AllGroupsQueryHandlerTest extends BaseTestCase
{
    public function testItAttemptsToGetAllGroupsFromGroupViewRepository()
    {
        $groupViewRepository = $this->createMock(GroupViewRepositoryInterface::class);
        $groupViewTransformer = $this->createMock(GroupDataTransformerInterface::class);
        $groupViewRepository->expects($this->once())->method('all')->willReturn([]);

        $allGroupsQuery = new AllGroupsQuery();
        $allGroupsQueryHandler = new AllGroupsQueryHandler($groupViewRepository, $groupViewTransformer);
        $allGroupsQueryHandler->handle($allGroupsQuery);
    }
}
