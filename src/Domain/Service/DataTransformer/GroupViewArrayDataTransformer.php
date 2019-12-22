<?php

namespace Tailgate\Domain\Service\DataTransformer;

use Tailgate\Domain\Service\DataTransformer\GroupDataTransformerInterface;
use Tailgate\Domain\Service\DataTransformer\FollowDataTransformerInterface;
use Tailgate\Domain\Service\DataTransformer\MemberDataTransformerInterface;
use Tailgate\Domain\Service\DataTransformer\PlayerDataTransformerInterface;
use Tailgate\Domain\Service\DataTransformer\ScoreDataTransformerInterface;
use Tailgate\Domain\Model\Group\GroupView;

class GroupViewArrayDataTransformer implements GroupDataTransformerInterface
{
    private $memberViewTransformer;
    private $playerViewTransformer;
    private $scoreViewTransformer;
    private $followViewTransformer;

    public function __construct(
        MemberDataTransformerInterface $memberViewTransformer,
        PlayerDataTransformerInterface $playerViewTransformer,
        ScoreDataTransformerInterface $scoreViewTransformer,
        FollowDataTransformerInterface $followViewTransformer
    ) {
        $this->memberViewTransformer = $memberViewTransformer;
        $this->playerViewTransformer = $playerViewTransformer;
        $this->scoreViewTransformer = $scoreViewTransformer;
        $this->followViewTransformer = $followViewTransformer;
    }

    public function read(GroupView $groupView)
    {
        $follow = null;
        $members = [];
        $players = [];
        $scores = [];

        if ($followView = $groupView->getFollow()) {
            $follow = $this->followViewTransformer->read($followView);
        }

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
            'follow'     => $follow,
            'members'    => $members,
            'players'    => $players,
            'scores'     => $scores,
        ];
    }
}
