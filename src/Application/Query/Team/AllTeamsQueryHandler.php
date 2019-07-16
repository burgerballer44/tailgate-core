<?php

namespace Tailgate\Application\Query\Team;

use Tailgate\Domain\Model\Team\TeamViewRepositoryInterface;
use Tailgate\Application\DataTransformer\TeamDataTransformerInterface;

class AllTeamsQueryHandler
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

    public function handle(AllTeamsQuery $allTeamsQuery)
    {
        $teamViews = $this->teamViewRepository->all();

        $teams = [];

        foreach ($teamViews as $teamView) {
            $teams[] = $this->teamViewTransformer->read($teamView);
        }

        return $teams;
    }
}