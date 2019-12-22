<?php

namespace Tailgate\Domain\Service\DataTransformer;

use Tailgate\Domain\Model\Season\GameView;

interface GameDataTransformerInterface
{
    public function read(GameView $gameView);
}
