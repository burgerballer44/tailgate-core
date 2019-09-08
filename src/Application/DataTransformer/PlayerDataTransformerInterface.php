<?php

namespace Tailgate\Application\DataTransformer;

use Tailgate\Domain\Model\Group\PlayerView;

interface PlayerDataTransformerInterface
{
    public function read(PlayerView $playerView);
}
