<?php

namespace Tailgate\Domain\Model\Season;

use Tailgate\Common\Projection\ProjectionInterface;

interface SeasonProjectionInterface extends ProjectionInterface
{
    public function projectSeasonCreated(SeasonCreated $event);
    public function projectGameAdded(GameAdded $event);
    public function projectGameDeleted(GameDeleted $event);
    public function projectGameScoreUpdated(GameScoreUpdated $event);
    public function projectSeasonDeleted(SeasonDeleted $event);
    public function projectSeasonUpdated(SeasonUpdated $event);
}
