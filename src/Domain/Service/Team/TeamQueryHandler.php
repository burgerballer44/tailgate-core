<?php

namespace Tailgate\Domain\Service\Team;

use Tailgate\Application\Query\Team\TeamQuery;
use Tailgate\Domain\Model\Group\FollowViewRepositoryInterface;
use Tailgate\Domain\Model\Season\GameViewRepositoryInterface;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamViewRepositoryInterface;
use Tailgate\Domain\Service\DataTransformer\TeamDataTransformerInterface;

class TeamQueryHandler
{
    private $teamViewRepository;
    private $teamViewTransformer;
    private $gameViewRepository;
    private $followViewRepository;

    public function __construct(
        TeamViewRepositoryInterface $teamViewRepository,
        FollowViewRepositoryInterface $followViewRepository,
        GameViewRepositoryInterface $gameViewRepository,
        TeamDataTransformerInterface $teamViewTransformer
    ) {
        $this->teamViewRepository = $teamViewRepository;
        $this->followViewRepository = $followViewRepository;
        $this->gameViewRepository = $gameViewRepository;
        $this->teamViewTransformer = $teamViewTransformer;
    }

    public function handle(TeamQuery $query)
    {
        $teamId = TeamId::fromString($query->getTeamId());

        $teamView = $this->teamViewRepository->get($teamId);
        $followViews = $this->followViewRepository->getAllByTeam($teamId);
        $gameViews = $this->gameViewRepository->getAllByTeam($teamId);

        $teamView->addFollowViews($followViews);
        $teamView->addGameViews($gameViews);

        return $this->teamViewTransformer->read($teamView);
    }
}
