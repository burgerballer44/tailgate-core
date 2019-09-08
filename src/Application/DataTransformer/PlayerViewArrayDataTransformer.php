<?php

namespace Tailgate\Application\DataTransformer;

use Tailgate\Application\DataTransformer\PlayerDataTransformerInterface;
use Tailgate\Domain\Model\Group\PlayerView;

class PlayerViewArrayDataTransformer implements PlayerDataTransformerInterface
{
    public function read(PlayerView $playerView)
    {
        return [
            'playerId' => $playerView->getPlayerId(),
            'memberId' => $playerView->getMemberId(),
            'groupId' => $playerView->getGroupId(),
            'username' => $playerView->getUsername()
        ];
    }
}
