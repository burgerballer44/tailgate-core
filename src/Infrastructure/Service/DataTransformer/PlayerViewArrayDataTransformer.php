<?php

namespace Tailgate\Infrastructure\Service\DataTransformer;

use Tailgate\Domain\Model\Group\PlayerView;
use Tailgate\Domain\Service\DataTransformer\PlayerDataTransformerInterface;

class PlayerViewArrayDataTransformer implements PlayerDataTransformerInterface
{
    public function read(PlayerView $playerView)
    {
        return [
            'playerId' => $playerView->getPlayerId(),
            'memberId' => $playerView->getMemberId(),
            'groupId' => $playerView->getGroupId(),
            'username' => $playerView->getUsername(),
        ];
    }
}
