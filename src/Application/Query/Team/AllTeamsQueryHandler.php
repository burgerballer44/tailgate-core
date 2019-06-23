<?php

namespace Tailgate\Application\Query\Team;

use Tailgate\Domain\Model\Team\TeamViewRepositoryInterface;

class AllTeamsQueryHandler
{
    private $teamViewRepository;

    public function __construct(TeamViewRepositoryInterface $teamViewRepository)
    {
        $this->teamViewRepository = $teamViewRepository;
    }

    public function handle(AllTeamsQuery $allTeamsQuery)
    {
        return $this->teamViewRepository->all();
    }
}