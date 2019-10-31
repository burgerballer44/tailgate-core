<?php

namespace Tailgate\Application\DataTransformer;

use Tailgate\Application\DataTransformer\GroupDataTransformerInterface;
use Tailgate\Application\DataTransformer\MemberDataTransformerInterface;
use Tailgate\Application\DataTransformer\PlayerDataTransformerInterface;
use Tailgate\Application\DataTransformer\ScoreDataTransformerInterface;
use Tailgate\Domain\Model\Group\GroupView;

class GroupViewArrayDataTransformer implements GroupDataTransformerInterface
{
    private $memberViewTransformer;
    private $playerViewTransformer;
    private $scoreViewTransformer;

    public function __construct(
        MemberDataTransformerInterface $memberViewTransformer,
        PlayerDataTransformerInterface $playerViewTransformer,
        ScoreDataTransformerInterface $scoreViewTransformer
    ) {
        $this->memberViewTransformer = $memberViewTransformer;
        $this->playerViewTransformer = $playerViewTransformer;
        $this->scoreViewTransformer = $scoreViewTransformer;
    }

    public function read(GroupView $groupView)
    {
        $members = [];
        $players = [];
        $scores = [];

        foreach ($groupView->getMembers() as $memberView) {
            $members[] = $this->memberViewTransformer->read($memberView);
        }

        foreach ($groupView->getPlayers() as $playerView) {
            $players[] = $this->playerViewTransformer->read($playerView);
        }

        foreach ($groupView->getScores() as $scoreView) {
            $scores[] = $this->scoreViewTransformer->read($scoreView);
        }

        return [
            'groupId'    => $groupView->getGroupId(),
            'name'       => $groupView->getName(),
            'inviteCode' => $groupView->getInviteCode(),
            'ownerId'    => $groupView->getOwnerId(),
            'members'    => $members,
            'players'    => $players,
            'scores'     => $scores,
        ];
    }
}
