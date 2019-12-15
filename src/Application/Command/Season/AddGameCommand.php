<?php

namespace Tailgate\Application\Command\Season;

class AddGameCommand
{
    private $seasonId;
    private $homeTeamId;
    private $awayTeamId;
    private $startDate;
    private $startTime;

    public function __construct(
        string $seasonId,
        string $homeTeamId,
        string $awayTeamId,
        string $startDate,
        string $startTime
    ) {
        $this->seasonId = $seasonId;
        $this->homeTeamId = $homeTeamId;
        $this->awayTeamId = $awayTeamId;
        $this->startDate = $startDate;
        $this->startTime = $startTime;
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

    public function getStartTime()
    {
        return $this->startTime;
    }
}
