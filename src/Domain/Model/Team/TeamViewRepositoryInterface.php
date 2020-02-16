<?php

namespace Tailgate\Domain\Model\Team;

use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Team\TeamId;

interface TeamViewRepositoryInterface
{
    public function get(TeamId $id);
    public function allBySport($sport);
    public function all();
}
