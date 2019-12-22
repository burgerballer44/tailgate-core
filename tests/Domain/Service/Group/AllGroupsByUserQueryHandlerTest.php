<?php

namespace Tailgate\Test\Domain\Service\Group;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Query\Group\AllGroupsByUserQuery;
use Tailgate\Domain\Service\Group\AllGroupsByUserQueryHandler;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Domain\Service\DataTransformer\GroupDataTransformerInterface;

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
