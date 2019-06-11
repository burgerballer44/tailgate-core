<?php

namespace Tailgate\Domain\Model\Season;

use Tailgate\Common\Projection\ProjectionInterface;

interface SeasonProjectionInterface extends ProjectionInterface
{
    public function projectSeasonCreated(SeasonCreated $event);
    public function projectGameAdded(GameAdded $event);
    public function projectGameScoreAdded(GameScoreAdded $event);
}