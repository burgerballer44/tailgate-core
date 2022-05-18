<?php

namespace Tailgate\Domain\Model\Season;

use Tailgate\Domain\Model\Team\TeamId;

interface GameViewRepositoryInterface
{
    public function get(GameId $id);

    public function getAllBySeason(SeasonId $seasonId);

    public function getAllByTeam(TeamId $teamId);

    public function getAllByTeamAndSeason(TeamId $teamId, SeasonId $seasonId);
}
