<?php

namespace Tailgate\Test\Application\Command\Team;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Query\Team\TeamQuery;
use Tailgate\Application\Query\Team\TeamQueryHandler;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamViewRepositoryInterface;

class TeamQueryHandlerTest extends TestCase
{
    public function testItAttemptsToGetATeamByTeamIdFromTeamViewRepository()
    {
        $teamId = 'teamId';

        $teamViewRepository = $this->createMock(TeamViewRepositoryInterface::class);
        $teamViewRepository->expects($this->once())
            ->method('get')  
            ->with($this->callback(function($teamQueryTeamId) use ($teamId) {
                return (new TeamId($teamId))->equals($teamQueryTeamId);
            }));

        $teamQuery = new TeamQuery($teamId);
        $teamQueryHandler = new TeamQueryHandler($teamViewRepository);
        $teamQueryHandler->handle($teamQuery);
    }
}
