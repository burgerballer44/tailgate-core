<?php

namespace Tailgate\Test\Domain\Service\Season;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Query\Season\AllFollowedGamesQuery;
use Tailgate\Domain\Model\Group\FollowId;
use Tailgate\Domain\Model\Group\FollowView;
use Tailgate\Domain\Model\Group\FollowViewRepositoryInterface;
use Tailgate\Domain\Model\Season\GameViewRepositoryInterface;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Service\DataTransformer\GameDataTransformerInterface;
use Tailgate\Domain\Service\Season\AllFollowedGamesQueryHandler;

class AllFollowedGamesQueryHandlerTest extends TestCase
{
    public function testItAttemptsToGetASeasonBySeasonIdFromTeamViewRepository()
    {
        $followId = 'followId';

        $followViewRepository = $this->createMock(FollowViewRepositoryInterface::class);
        $gameViewRepository = $this->createMock(GameViewRepositoryInterface::class);
        $gameViewTransformer = $this->createMock(GameDataTransformerInterface::class);

        $followView = ['team_id' => 'team_id', 'season_id' => 'season_id'];
        $followViewRepository->expects($this->once())
            ->method('get')
            ->willReturn($followView)
            ->with($this->callback(function ($allFollowedGamesQueryFollowId) use ($followId) {
                return (new FollowId($followId))->equals($allFollowedGamesQueryFollowId);
            }));

        $gameViewRepository->expects($this->once())
            ->method('getAllByTeamAndSeason')
            ->willReturn([])
            ->with($this->callback(function ($returnedFollowViewTeamId) use ($followView) {
                    return (new TeamId($followView['team_id']))->equals($returnedFollowViewTeamId);
                }),
                $this->callback(function ($returnedFollowViewSeasonId) use ($followView) {
                    return (new SeasonId($followView['season_id']))->equals($returnedFollowViewSeasonId);
                })
            );

        $allFollowedGamesQuery = new AllFollowedGamesQuery($followId);
        $allFollowedGamesQueryHandler = new AllFollowedGamesQueryHandler($followViewRepository, $gameViewRepository, $gameViewTransformer);
        $allFollowedGamesQueryHandler->handle($allFollowedGamesQuery);
    }
}
