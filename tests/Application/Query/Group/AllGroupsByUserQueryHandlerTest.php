<?php

namespace Tailgate\Test\Application\Command\Group;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Query\Group\AllGroupsByUserQuery;
use Tailgate\Application\Query\Group\AllGroupsByUserQueryHandler;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Application\DataTransformer\GroupDataTransformerInterface;

class AllGroupsByUserQueryHandlerTest extends TestCase
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
