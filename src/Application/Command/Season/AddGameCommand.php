<?php

namespace Tailgate\Application\Command\Season;

class AddGameCommand
{
    private $seasonId;
    private $homeTeamId;
    private $awayTeamId;
    private $startDate;

    public function __construct(
        string $seasonId,
        string $homeTeamId,
        string $awayTeamId,
        string $startDate
    ) {
        $this->seasonId = $seasonId;
        $this->homeTeamId = $homeTeamId;
        $this->awayTeamId = $awayTeamId;
        $this->startDate = $startDate;
    }

    public function getSeasonId()
    {
        return $this->seasonId;
    }

    public function getHomeTeamId()
    {
        return $this->homeTeamId;
    }

    public function getAwayTeamId()
    {
        return $this->awayTeamId;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }
}