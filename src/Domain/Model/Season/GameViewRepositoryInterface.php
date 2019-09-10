<?php

namespace Tailgate\Domain\Model\Season;

use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\GameId;

interface GameViewRepositoryInterface
{
    public function get(GameId $id);
    public function all();
    public function getAllBySeason(SeasonId $seasonId);
}
