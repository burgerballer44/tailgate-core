<?php

namespace Tailgate\Infrastructure\Persistence\Projection;

use Tailgate\Domain\Model\Season\GameAdded;
use Tailgate\Domain\Model\Season\GameDeleted;
use Tailgate\Domain\Model\Season\GameScoreUpdated;
use Tailgate\Domain\Model\Season\SeasonCreated;
use Tailgate\Domain\Model\Season\SeasonDeleted;
use Tailgate\Domain\Model\Season\SeasonUpdated;

interface SeasonProjectionInterface extends ProjectionInterface
{
    public function projectSeasonCreated(SeasonCreated $event);

    public function projectGameAdded(GameAdded $event);

    public function projectGameDeleted(GameDeleted $event);

    public function projectGameScoreUpdated(GameScoreUpdated $event);

    public function projectSeasonDeleted(SeasonDeleted $event);

    public function projectSeasonUpdated(SeasonUpdated $event);
}
