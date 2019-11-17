<?php

namespace Tailgate\Domain\Model\Season;

use Tailgate\Domain\Model\Season\SeasonId;

interface SeasonViewRepositoryInterface
{
    public function get(SeasonId $id);
    public function allBySport($sport);
    public function all();
}
