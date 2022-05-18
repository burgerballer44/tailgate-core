<?php

namespace Tailgate\Domain\Model\Season;

interface SeasonViewRepositoryInterface
{
    public function get(SeasonId $id);

    public function allBySport($sport);

    public function all();
}
