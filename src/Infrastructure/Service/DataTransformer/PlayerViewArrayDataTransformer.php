<?php

namespace Tailgate\Infrastructure\Service\DataTransformer;

use Tailgate\Domain\Model\Group\PlayerView;
use Tailgate\Domain\Service\DataTransformer\PlayerDataTransformerInterface;

class PlayerViewArrayDataTransformer implements PlayerDataTransformerInterface
{
    public function read(PlayerView $playerView)
    {
        return [
            'player_id' => $playerView->getPlayerId(),
            'member_id' => $playerView->getMemberId(),
            'group_id' => $playerView->getGroupId(),
            'username' => $playerView->getUsername(),
        ];
    }
}
