<?php

namespace Tailgate\Domain\Model\Season;

use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Team\TeamId;

interface GameViewRepositoryInterface
{
    public function get(GameId $id);
    public function getAllBySeason(SeasonId $seasonId);
    public function getAllByTeam(TeamId $teamIf);
}
