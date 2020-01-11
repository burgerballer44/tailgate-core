<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\Season\GameId;

class Score
{
    private $scoreId;
    private $groupId;
    private $playerId;
    private $gameId;
    private $homeTeamPrediction;
    private $awayTeamPrediction;

    private function __construct(
        $scoreId,
        $groupId,
        $playerId,
        $gameId,
        $homeTeamPrediction,
        $awayTeamPrediction
    ) {
        $this->scoreId = $scoreId;
        $this->groupId = $groupId;
        $this->playerId = $playerId;
        $this->gameId = $gameId;
        $this->homeTeamPrediction = $homeTeamPrediction;
        $this->awayTeamPrediction = $awayTeamPrediction;
    }

    public static function create(
        GroupId $groupId,
        ScoreId $scoreId,
        PlayerId $playerId,
        GameId $gameId,
        $homeTeamPrediction,
        $awayTeamPrediction
    ) {
        $newScore = new Score(
            $scoreId,
            $groupId,
            $playerId,
            $gameId,
            $homeTeamPrediction,
            $awayTeamPrediction
        );

        return $newScore;
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

    public function update($homeTeamPrediction, $awayTeamPrediction)
    {
        $this->homeTeamPrediction = $homeTeamPrediction;
        $this->awayTeamPrediction = $awayTeamPrediction;
    }
}
