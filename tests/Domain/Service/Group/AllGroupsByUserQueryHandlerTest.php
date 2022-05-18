<?php

namespace Tailgate\Test\Domain\Service\Group;

use Tailgate\Application\Query\Group\AllGroupsByUserQuery;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Domain\Service\DataTransformer\GroupDataTransformerInterface;
use Tailgate\Domain\Service\Group\AllGroupsByUserQueryHandler;
use Tailgate\Test\BaseTestCase;

class AllGroupsByUserQueryHandlerTest extends BaseTestCase
{
    public function testItAttemptsToGetAllGroupsFromGroupViewRepository()
    {
        $userId = 'userId';

        $groupViewRepository = $this->createMock(GroupViewRepositoryInterface::class);
        $groupViewTransformer = $this->createMock(GroupDataTransformerInterface::class);
        $groupViewRepository->expects($this->once())->method('allByUser')->willReturn([]);

        $allGroupsByUserQuery = new AllGroupsByUserQuery($userId);
        $allGroupsByUserQueryHandler = new AllGroupsByUserQueryHandler($groupViewRepository, $groupViewTransformer);
        $allGroupsByUserQueryHandler->handle($allGroupsByUserQuery);
    }
}
