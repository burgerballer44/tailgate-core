<?php

namespace Tailgate\Test\Application\Command\Team;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Query\Team\AllTeamsQuery;
use Tailgate\Application\Query\Team\AllTeamsQueryHandler;
use Tailgate\Domain\Model\Team\TeamViewRepositoryInterface;
use Tailgate\Application\DataTransformer\TeamDataTransformerInterface;

class AllTeamsQueryHandlerTest extends TestCase
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
