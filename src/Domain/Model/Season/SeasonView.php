<?php

namespace Tailgate\Domain\Model\Season;

class SeasonView
{
    private $season_id;
    private $sport;
    private $seasonType;
    private $name;
    private $season_start;
    private $season_end;
    private $games = [];

    public function __construct(
        $season_id,
        $sport,
        $seasonType,
        $name,
        $season_start,
        $season_end
    ) {
        $this->season_id = $season_id;
        $this->sport = $sport;
        $this->seasonType = $seasonType;
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

    public function getSeasonType()
    {
        return $this->seasonType;
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

    public function getGames()
    {
        return $this->games;
    }

    public function addGameViews($gameView)
    {
        if (is_array($gameView)) {
            $this->games = $gameView;
        } else {
            $this->games[] = $gameView;
        }
    }
}
