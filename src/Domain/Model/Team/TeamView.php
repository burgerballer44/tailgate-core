<?php

namespace Tailgate\Domain\Model\Team;

class TeamView
{
    private $teamId;
    private $designation;
    private $mascot;
    private $sport;
    private $follows = [];
    private $games = [];

    public function __construct($teamId, $designation, $mascot, $sport)
    {
        $this->teamId = $teamId;
        $this->designation = $designation;
        $this->mascot = $mascot;
        $this->sport = $sport;
    }

    public function getTeamId()
    {
        return $this->teamId;
    }

    public function getDesignation()
    {
        return $this->designation;
    }

    public function getMascot()
    {
        return $this->mascot;
    }

    public function getSport()
    {
        return $this->sport;
    }

    public function getFollows()
    {
        return $this->follows;
    }

    public function getGames()
    {
        return $this->games;
    }

    public function addFollowViews($followView)
    {
        if (is_array($followView)) {
            $this->follows = $followView;
        } else {
            $this->follows[] = $followView;
        }
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
