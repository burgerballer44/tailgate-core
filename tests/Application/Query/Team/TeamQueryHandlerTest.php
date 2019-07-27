<?php

namespace Tailgate\Test\Application\Command\Team;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Query\Team\TeamQuery;
use Tailgate\Application\Query\Team\TeamQueryHandler;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamView;
use Tailgate\Domain\Model\Team\TeamViewRepositoryInterface;
use Tailgate\Application\DataTransformer\TeamDataTransformerInterface;

class TeamQueryHandlerTest extends TestCase
{
    public function testItAttemptsToGetATeamByTeamIdFromTeamViewRepository()
    {
        $teamId = 'teamId';

        $teamViewRepository = $this->createMock(TeamViewRepositoryInterface::class);
        $teamViewTransformer = $this->createMock(TeamDataTransformerInterface::class);
        $teamView = $this->createMock(TeamView::class);
        $teamViewRepository->expects($this->once())
            ->method('get')
            ->willReturn($teamView)
            ->with($this->callback(function($teamQueryTeamId) use ($teamId) {
                return (new TeamId($teamId))->equals($teamQueryTeamId);
            }));

        $teamQuery = new TeamQuery($teamId);
        $teamQueryHandler = new TeamQueryHandler($teamViewRepository, $teamViewTransformer);
        $teamQueryHandler->handle($teamQuery);
    }
}