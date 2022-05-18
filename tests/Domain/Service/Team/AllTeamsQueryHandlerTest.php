<?php

namespace Tailgate\Test\Domain\Service\Team;

use Tailgate\Application\Query\Team\AllTeamsQuery;
use Tailgate\Domain\Model\Team\TeamViewRepositoryInterface;
use Tailgate\Domain\Service\DataTransformer\TeamDataTransformerInterface;
use Tailgate\Domain\Service\Team\AllTeamsQueryHandler;
use Tailgate\Test\BaseTestCase;

class AllTeamsQueryHandlerTest extends BaseTestCase
{
    public function testItAttemptsToGetAllTeamsFromTeamViewRepository()
    {
        $teamViewRepository = $this->createMock(TeamViewRepositoryInterface::class);
        $teamViewTransformer = $this->createMock(TeamDataTransformerInterface::class);
        $teamViewRepository->expects($this->once())->method('all')->willReturn([]);

        $allTeamsQuery = new AllTeamsQuery();
        $allTeamsQueryHandler = new AllTeamsQueryHandler($teamViewRepository, $teamViewTransformer);
        $allTeamsQueryHandler->handle($allTeamsQuery);
    }
}
