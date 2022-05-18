<?php

namespace Tailgate\Test\Domain\Service\Team;

use Tailgate\Application\Query\Team\TeamsBySportQuery;
use Tailgate\Domain\Model\Season\Sport;
use Tailgate\Domain\Model\Team\TeamView;
use Tailgate\Domain\Model\Team\TeamViewRepositoryInterface;
use Tailgate\Domain\Service\DataTransformer\TeamDataTransformerInterface;
use Tailgate\Domain\Service\Team\TeamsBySportQueryHandler;
use Tailgate\Test\BaseTestCase;

class TeamsBySportQueryHandlerTest extends BaseTestCase
{
    public function testItAttemptsToGetAllTeamsBySportFromTeamViewRepository()
    {
        $sport = Sport::getFootball();

        $teamViewRepository = $this->createMock(TeamViewRepositoryInterface::class);
        $teamViewTransformer = $this->createMock(TeamDataTransformerInterface::class);
        $teamView = $this->createMock(TeamView::class);
        $teamViewRepository->expects($this->once())
            ->method('allBySport')
            ->willReturn($teamView)
            ->with($this->callback(function ($teamsBySportQuerySport) use ($sport) {
                return $sport == $teamsBySportQuerySport;
            }));

        $teamsBySportQuery = new TeamsBySportQuery($sport);
        $teamsBySportQueryHandler = new TeamsBySportQueryHandler($teamViewRepository, $teamViewTransformer);
        $teamsBySportQueryHandler->handle($teamsBySportQuery);
    }
}
