<?php

namespace Tailgate\Domain\Service\Team;

use Tailgate\Application\Query\Team\TeamsBySportQuery;
use Tailgate\Domain\Model\Team\TeamViewRepositoryInterface;
use Tailgate\Domain\Service\DataTransformer\TeamDataTransformerInterface;

class TeamsBySportQueryHandler
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

    public function handle(TeamsBySportQuery $query)
    {
        $sport = $query->getSport();

        $teamViews = $this->teamViewRepository->allBySport($sport);

        $teams = [];

        foreach ($teamViews as $teamView) {
            $teams[] = $this->teamViewTransformer->read($teamView);
        }

        return $teams;
    }
}
