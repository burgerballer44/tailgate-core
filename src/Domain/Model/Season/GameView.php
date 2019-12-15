<?php

namespace Tailgate\Domain\Model\Season;

class GameView
{
    private $seasonId;
    private $gameId;
    private $homeTeamId;
    private $awayTeamId;
    private $homeTeamScore;
    private $awayTeamScore;
    private $startDate;
    private $startTime;
    private $homeDesignation;
    private $homeMascot;
    private $awayDesignation;
    private $awayMascot;

    public function __construct(
        $seasonId,
        $gameId,
        $homeTeamId,
        $awayTeamId,
        $homeTeamScore,
        $awayTeamScore,
        $startDate,
        $startTime,
        $homeDesignation,
        $homeMascot,
        $awayDesignation,
        $awayMascot
    ) {
        $this->seasonId = $seasonId;
        $this->gameId = $gameId;
        $this->homeTeamId = $homeTeamId;
        $this->awayTeamId = $awayTeamId;
        $this->homeTeamScore = $homeTeamScore;
        $this->awayTeamScore = $awayTeamScore;
        $this->startDate = $startDate;
        $this->startTime = $startTime;
        $this->homeDesignation = $homeDesignation;
        $this->homeMascot = $homeMascot;
        $this->awayDesignation = $awayDesignation;
        $this->awayMascot = $awayMascot;
    }

    public function getSeasonId()
    {
        return $this->seasonId;
    }

    public function getGameId()
    {
        return $this->gameId;
    }

    public function getHomeTeamId()
    {
        return $this->homeTeamId;
    }

    public function getAwayTeamId()
    {
        return $this->awayTeamId;
    }

    public function getHomeTeamScore()
    {
        return $this->homeTeamScore;
    }

    public function getAwayTeamScore()
    {
        return $this->awayTeamScore;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function getHomeDesignation()
    {
        return $this->homeDesignation;
    }

    public function getHomeMascot()
    {
        return $this->homeMascot;
    }

    public function getAwayDesignation()
    {
        return $this->awayDesignation;
    }

    public function getAwayMascot()
    {
        return $this->awayMascot;
    }
}
