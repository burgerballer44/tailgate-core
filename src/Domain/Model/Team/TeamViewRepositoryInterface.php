<?php

namespace Tailgate\Domain\Model\Team;

interface TeamViewRepositoryInterface
{
    public function get(TeamId $id);

    public function allBySport($sport);

    public function all();
}
