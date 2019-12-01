<?php

namespace Tailgate\Application\DataTransformer;

use Tailgate\Application\DataTransformer\TeamDataTransformerInterface;
use Tailgate\Application\DataTransformer\FollowDataTransformerInterface;
use Tailgate\Application\DataTransformer\GameDataTransformerInterface;
use Tailgate\Domain\Model\Team\TeamView;

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
            $$follows[] = $this->followViewTransformer->read($followView);
        }

        foreach ($teamView->getGames() as $gameView) {
            $games[] = $this->gameViewTransformer->read($gameView);
        }

        return [
            'teamId'      => $teamView->getTeamId(),
            'designation' => $teamView->getDesignation(),
            'mascot'      => $teamView->getMascot(),
            'follows'     => $follows,
            'games'       => $games,
        ];
    }
}
