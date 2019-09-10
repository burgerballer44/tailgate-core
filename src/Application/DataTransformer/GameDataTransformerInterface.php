<?php

namespace Tailgate\Application\DataTransformer;

use Tailgate\Domain\Model\Season\GameView;

interface GameDataTransformerInterface
{
    public function read(GameView $gameView);
}
