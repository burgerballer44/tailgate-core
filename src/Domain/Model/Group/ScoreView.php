<?php

namespace Tailgate\Domain\Model\Group;

class ScoreView
{
    private $scoreId;
    private $groupId;
    private $playerId;
    private $gameId;
    private $homeTeamPrediction;
    private $awayTeamPrediction;
    private $homeTeamId;
    private $awayTeamId;
    private $homeDesignation;
    private $homeMascot;
    private $awayDesignation;
    private $awayMascot;

    public function __construct(
        $scoreId,
        $groupId,
        $playerId,
        $gameId,
        $homeTeamPrediction,
        $awayTeamPrediction,
        $homeTeamId,
        $awayTeamId,
        $homeDesignation,
        $homeMascot,
        $awayDesignation,
        $awayMascot
    ) {
        $this->scoreId = $scoreId;
        $this->groupId = $groupId;
        $this->playerId = $playerId;
        $this->gameId = $gameId;
        $this->homeTeamPrediction = $homeTeamPrediction;
        $this->awayTeamPrediction = $awayTeamPrediction;
        $this->homeTeamId = $homeTeamId;
        $this->awayTeamId = $awayTeamId;
        $this->homeDesignation = $homeDesignation;
        $this->homeMascot = $homeMascot;
        $this->awayDesignation = $awayDesignation;
        $this->awayMascot = $awayMascot;
    }

    public function getScoreId()
    {
        return $this->scoreId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function getPlayerId()
    {
        return $this->playerId;
    }

    public function getGameId()
    {
        return $this->gameId;
    }

    public function getHomeTeamPrediction()
    {
        return $this->homeTeamPrediction;
    }

    public function getAwayTeamPrediction()
    {
        return $this->awayTeamPrediction;
    }
    public function getHomeTeamId()
    {
        return $this->homeTeamId;
    }

    public function getAwayTeamId()
    {
        return $this->awayTeamId;
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
