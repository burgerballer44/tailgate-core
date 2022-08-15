<?php

namespace Tailgate\Infrastructure\Service\DataTransformer;

use Tailgate\Domain\Model\Team\TeamView;
use Tailgate\Domain\Service\DataTransformer\FollowDataTransformerInterface;
use Tailgate\Domain\Service\DataTransformer\GameDataTransformerInterface;
use Tailgate\Domain\Service\DataTransformer\TeamDataTransformerInterface;

class TeamViewArrayDataTransformer implements TeamDataTransformerInterface
{
    private $followViewTransformer;
    private $gameViewTransformer;

    public function __construct(
        FollowDataTransformerInterface $followViewTransformer,
        GameDataTransformerInterface $gameViewTransformer
    ) {
        $this->followViewTransformer = $followViewTransformer;
        $this->gameViewTransformer = $gameViewTransformer;
    }

    public function read(TeamView $teamView)
    {
        $follows = [];
        $games = [];

        foreach ($teamView->getFollows() as $followView) {
            $follows[] = $this->followViewTransformer->read($followView);
        }

        foreach ($teamView->getGames() as $gameView) {
            $games[] = $this->gameViewTransformer->read($gameView);
        }

        return [
            'team_id' => $teamView->getTeamId(),
            'designation' => $teamView->getDesignation(),
            'mascot' => $teamView->getMascot(),
            'sport' => $teamView->getSport(),
            'follows' => $follows,
            'games' => $games,
        ];
    }
}
