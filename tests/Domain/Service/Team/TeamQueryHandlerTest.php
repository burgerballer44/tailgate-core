<?php

namespace Tailgate\Test\Domain\Service\Team;

use Tailgate\Application\Query\Team\TeamQuery;
use Tailgate\Domain\Model\Group\FollowViewRepositoryInterface;
use Tailgate\Domain\Model\Season\GameViewRepositoryInterface;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamView;
use Tailgate\Domain\Model\Team\TeamViewRepositoryInterface;
use Tailgate\Domain\Service\DataTransformer\TeamDataTransformerInterface;
use Tailgate\Domain\Service\Team\TeamQueryHandler;
use Tailgate\Test\BaseTestCase;

class TeamQueryHandlerTest extends BaseTestCase
{
    public function testItAttemptsToGetATeamByTeamIdFromTeamViewRepository()
    {
        $teamId = 'teamId';

        $teamViewRepository = $this->createMock(TeamViewRepositoryInterface::class);
        $followViewRepository = $this->createMock(FollowViewRepositoryInterface::class);
        $gameViewRepository = $this->createMock(GameViewRepositoryInterface::class);
        $teamViewTransformer = $this->createMock(TeamDataTransformerInterface::class);
        $teamView = $this->createMock(TeamView::class);
        $teamViewRepository->expects($this->once())
            ->method('get')
            ->willReturn($teamView)
            ->with($this->callback(function ($teamQueryTeamId) use ($teamId) {
                return (new TeamId($teamId))->equals($teamQueryTeamId);
            }));

        $teamQuery = new TeamQuery($teamId);
        $teamQueryHandler = new TeamQueryHandler($teamViewRepository, $followViewRepository, $gameViewRepository, $teamViewTransformer);
        $teamQueryHandler->handle($teamQuery);
    }
}
