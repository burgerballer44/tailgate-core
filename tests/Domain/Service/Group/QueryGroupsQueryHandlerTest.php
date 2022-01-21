<?php

namespace Tailgate\Test\Domain\Service\Group;

use Tailgate\Test\BaseTestCase;
use Tailgate\Application\Query\Group\QueryGroupsQuery;
use Tailgate\Domain\Service\Group\QueryGroupsQueryHandler;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Domain\Service\DataTransformer\GroupDataTransformerInterface;

class QueryGroupsQueryHandlerTest extends BaseTestCase
{
    public function testItAttemptsToQueryGroupsFromGroupViewRepository()
    {
        $userId = 'userId';
        $name = 'name';

        $groupViewRepository = $this->createMock(GroupViewRepositoryInterface::class);
        $groupViewTransformer = $this->createMock(GroupDataTransformerInterface::class);
        $groupViewRepository->expects($this->once())->method('query')->willReturn([]);

        $queryGroupsQuery = new QueryGroupsQuery($userId, $name);
        $queryGroupsQueryHandler = new QueryGroupsQueryHandler($groupViewRepository, $groupViewTransformer);
        $queryGroupsQueryHandler->handle($queryGroupsQuery);
    }
}
