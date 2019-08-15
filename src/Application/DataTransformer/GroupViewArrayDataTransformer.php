<?php

namespace Tailgate\Application\DataTransformer;

use Tailgate\Application\DataTransformer\GroupDataTransformerInterface;
use Tailgate\Application\DataTransformer\MemberDataTransformerInterface;
use Tailgate\Application\DataTransformer\ScoreDataTransformerInterface;
use Tailgate\Domain\Model\Group\GroupView;

class GroupViewArrayDataTransformer implements GroupDataTransformerInterface
{
    private $memberViewTransformer;
    private $scoreViewTransformer;

    public function __construct(
        MemberDataTransformerInterface $memberViewTransformer,
        ScoreDataTransformerInterface $scoreViewTransformer
    ) {
        $this->memberViewTransformer = $memberViewTransformer;
        $this->scoreViewTransformer = $scoreViewTransformer;
    }

    public function read(GroupView $groupView)
    {
        $members = [];
        $scores = [];

        foreach ($groupView->getMembers() as $memberView) {
            $members[] = $this->memberViewTransformer->read($memberView);
        }

        foreach ($groupView->getScores() as $scoreView) {
            $scores[] = $this->scoreViewTransformer->read($scoreView);
        }

        return [
            'groupId' => $groupView->getGroupId(),
            'name'    => $groupView->getName(),
            'ownerId' => $groupView->getOwnerId(),
            'members' => $members,
            'scores' => $scores,
        ];
    }
}
