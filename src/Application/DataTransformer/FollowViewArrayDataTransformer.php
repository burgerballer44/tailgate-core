<?php

namespace Tailgate\Application\DataTransformer;

use Tailgate\Application\DataTransformer\FollowDataTransformerInterface;
use Tailgate\Domain\Model\Team\FollowView;

class FollowViewArrayDataTransformer implements FollowDataTransformerInterface
{
    public function read(FollowView $followView)
    {
        return [
            'teamId' => $followView->getTeamId(),
            'followId' => $followView->getFollowId(),
            'groupId' => $followView->getGroupId(),
            'groupName' => $followView->getGroupName(),
            'teamDesignation' => $followView->getTeamDesignation(),
            'teamMascot' => $followView->getTeamMascot()
        ];
    }
}
