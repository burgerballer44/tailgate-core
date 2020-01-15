<?php

namespace Tailgate\Domain\Service\Season;

use Tailgate\Application\Query\Season\AllFollowedGamesQuery;
use Tailgate\Domain\Model\Group\FollowId;
use Tailgate\Domain\Model\Group\FollowViewRepositoryInterface;
use Tailgate\Domain\Model\Season\GameViewRepositoryInterface;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Service\DataTransformer\GameDataTransformerInterface;

class AllFollowedGamesQueryHandler
{
    private $followViewRepository;
    private $gameViewRepository;
    private $gameViewTransformer;

    public function __construct(
        FollowViewRepositoryInterface $followViewRepository,
        GameViewRepositoryInterface $gameViewRepository,
        GameDataTransformerInterface $gameViewTransformer
    ) {
        $this->followViewRepository = $followViewRepository;
        $this->gameViewRepository = $gameViewRepository;
        $this->gameViewTransformer = $gameViewTransformer;
    }

    public function handle(AllFollowedGamesQuery $query)
    {
        $followId = FollowId::fromString($query->getFollowId());
        $followView = $this->followViewRepository->get($followId);

        $teamId = TeamId::fromString($followView->getTeamId());
        $seasonId = SeasonId::fromString($followView->getSeasonId());
        $gameViews = $this->gameViewRepository->getAllByTeamAndSeason($teamId, $seasonId);

        $games = [];

        foreach ($gameViews as $gameView) {
            $games[] = $this->gameViewTransformer->read($gameView);
        }

        return $games;
    }
}
