<?php

namespace Tailgate\Application\Query\Team;

use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamViewRepositoryInterface;

class TeamQueryHandler
{
    private $teamViewRepository;

    public function __construct(TeamViewRepositoryInterface $teamViewRepository)
    {
        $this->teamViewRepository = $teamViewRepository;
    }

    public function handle(TeamQuery $teamQuery)
    {
        return $this->teamViewRepository->get(TeamId::fromString($teamQuery->getTeamId()));
    }
}