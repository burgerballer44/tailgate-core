<?php

namespace Tailgate\Application\Query\Team;

use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamViewRepositoryInterface;
use Tailgate\Application\DataTransformer\TeamDataTransformerInterface;

class TeamQueryHandler
{
    private $teamViewRepository;
    private $teamViewTransformer;

    public function __construct(
        TeamViewRepositoryInterface $teamViewRepository,
        TeamDataTransformerInterface $teamViewTransformer
    ) {
        $this->teamViewRepository = $teamViewRepository;
        $this->teamViewTransformer = $teamViewTransformer;
    }

    public function handle(TeamQuery $query)
    {
        $teamView = $this->teamViewRepository->get(TeamId::fromString($query->getTeamId()));
        return $this->teamViewTransformer->read($teamView);
    }
}
