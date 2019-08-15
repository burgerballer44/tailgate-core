<?php

namespace Tailgate\Domain\Model\Group;

class ScoreView
{
    private $scoreId;
    private $groupId;
    private $userId;
    private $gameId;
    private $homeTeamPrediction;
    private $awayTeamPrediction;

    public function __construct(
        $scoreId,
        $groupId,
        $userId,
        $gameId,
        $homeTeamPrediction,
        $awayTeamPrediction
    ) {
        $this->scoreId = $scoreId;
        $this->groupId = $groupId;
        $this->userId = $userId;
        $this->gameId = $gameId;
        $this->homeTeamPrediction = $homeTeamPrediction;
        $this->awayTeamPrediction = $awayTeamPrediction;
    }

    public function getScoreId()
    {
        return $this->scoreId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function getUserId()
    {
        return $this->userId;
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
}
