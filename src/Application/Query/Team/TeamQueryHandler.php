<?php

namespace Tailgate\Application\Query\Team;

use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamViewRepositoryInterface;
use Tailgate\Domain\Model\Team\FollowViewRepositoryInterface;
use Tailgate\Application\DataTransformer\TeamDataTransformerInterface;

class TeamQueryHandler
{
    private $teamViewRepository;
    private $teamViewTransformer;
    private $followViewRepository;

    public function __construct(
        TeamViewRepositoryInterface $teamViewRepository,
        FollowViewRepositoryInterface $followViewRepository,
        TeamDataTransformerInterface $teamViewTransformer
    ) {
        $this->teamViewRepository = $teamViewRepository;
        $this->followViewRepository = $followViewRepository;
        $this->teamViewTransformer = $teamViewTransformer;
    }

    public function handle(TeamQuery $query)
    {
        $teamId = TeamId::fromString($query->getTeamId());

        $teamView = $this->teamViewRepository->get($teamId);
        $followViews = $this->followViewRepository->getAllByTeam($teamId);

        $teamView->addFollowViews($followViews);

        return $this->teamViewTransformer->read($teamView);
    }
}
