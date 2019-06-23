<?php

namespace Tailgate\Test\Application\Command\Team;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Query\Team\AllTeamsQuery;
use Tailgate\Application\Query\Team\AllTeamsQueryHandler;
use Tailgate\Domain\Model\Team\TeamViewRepositoryInterface;

class AllTeamsQueryHandlerTest extends TestCase
{
    public function testItAttemptsToGetAllTeamsFromTeamViewRepository()
    {
        $teamViewRepository = $this->createMock(TeamViewRepositoryInterface::class);
        $teamViewRepository->expects($this->once())->method('all');

        $allTeamsQuery = new AllTeamsQuery();
        $allTeamsQueryHandler = new AllTeamsQueryHandler($teamViewRepository);
        $allTeamsQueryHandler->handle($allTeamsQuery);
    }
}
