<?php

namespace Tailgate\Application\DataTransformer;

use Tailgate\Application\DataTransformer\TeamDataTransformerInterface;
use Tailgate\Application\DataTransformer\FollowDataTransformerInterface;
use Tailgate\Domain\Model\Team\TeamView;

class TeamViewArrayDataTransformer implements TeamDataTransformerInterface
{
    private $followViewTransformer;

    public function __construct(FollowDataTransformerInterface $followViewTransformer)
    {
        $this->followViewTransformer = $followViewTransformer;
    }

    public function read(TeamView $teamView)
    {
        $follows = [];

        foreach ($teamView->getFollows() as $followView) {
            $follows[] = $this->followViewTransformer->read($followView);
        }

        return [
            'teamId'      => $teamView->getTeamId(),
            'designation' => $teamView->getDesignation(),
            'mascot'      => $teamView->getMascot(),
            'follows'     => $follows,
        ];
    }
}
