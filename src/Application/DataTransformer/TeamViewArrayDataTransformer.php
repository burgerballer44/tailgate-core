<?php

namespace Tailgate\Application\DataTransformer;

use Tailgate\Application\DataTransformer\TeamDataTransformerInterface;
use Tailgate\Domain\Model\Team\TeamView;

class TeamViewArrayDataTransformer implements TeamDataTransformerInterface
{
    public function read(TeamView $teamView)
    {
        return [
            'teamId'      => $teamView->getTeamId(),
            'designation' => $teamView->getDesignation(),
            'mascot'      => $teamView->getMascot(),
        ];
    }
}