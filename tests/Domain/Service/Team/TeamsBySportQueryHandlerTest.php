<?php

namespace Tailgate\Test\Domain\Service\Team;

use Tailgate\Test\BaseTestCase;
use Tailgate\Application\Query\Team\TeamsBySportQuery;
use Tailgate\Domain\Model\Group\FollowViewRepositoryInterface;
use Tailgate\Domain\Model\Season\GameViewRepositoryInterface;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\Sport;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamView;
use Tailgate\Domain\Model\Team\TeamViewRepositoryInterface;
use Tailgate\Domain\Service\DataTransformer\TeamDataTransformerInterface;
use Tailgate\Domain\Service\Team\TeamsBySportQueryHandler;

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
