<?php

namespace Tailgate\Domain\Service\Team;

use Tailgate\Domain\Model\Team\TeamViewRepositoryInterface;
use Tailgate\Domain\Service\DataTransformer\TeamDataTransformerInterface;

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

    public function handle()
    {
        $teamViews = $this->teamViewRepository->all();

        $teams = [];

        foreach ($teamViews as $teamView) {
            $teams[] = $this->teamViewTransformer->read($teamView);
        }

        return $teams;
    }
}
