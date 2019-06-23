<?php

namespace Tailgate\Domain\Model\Season;

class SeasonView
{
    private $season_id;
    private $sport;
    private $type;
    private $name;
    private $season_start;
    private $season_end;

    public function __construct(
        $season_id,
        $sport,
        $type,
        $name,
        $season_start,
        $season_end
    ) {
        $this->season_id = $season_id;
        $this->sport = $sport;
        $this->type = $type;
        $this->name = $name;
        $this->season_start = $season_start;
        $this->season_end = $season_end;
    }

    public function getSeasonId()
    {
        return $this->season_id;
    }

    public function getSport()
    {
        return $this->sport;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSeasonStart()
    {
        return $this->season_start;
    }

    public function getSeasonEnd()
    {
        return $this->season_end;
    }
}