<?php

namespace Tailgate\Application\Command\Season;

class DeleteSeasonCommand
{
    private $seasonId;

    public function __construct($seasonId)
    {
        $this->seasonId = $seasonId;
    }

    public function getSeasonId()
    {
        return $this->seasonId;
    }
}
