<?php

namespace Tailgate\Infrastructure\Service\DataTransformer;

use Tailgate\Domain\Model\Group\FollowView;
use Tailgate\Domain\Service\DataTransformer\FollowDataTransformerInterface;

class FollowViewArrayDataTransformer implements FollowDataTransformerInterface
{
    public function read(FollowView $followView)
    {
        return [
            'group_id' => $followView->getGroupId(),
            'follow_id' => $followView->getFollowId(),
            'team_id' => $followView->getTeamId(),
            'season_id' => $followView->getSeasonId(),
            'group_name' => $followView->getGroupName(),
            'team_designation' => $followView->getTeamDesignation(),
            'team_mascot' => $followView->getTeamMascot(),
            'season_name' => $followView->getSeasonName(),
        ];
    }
}
