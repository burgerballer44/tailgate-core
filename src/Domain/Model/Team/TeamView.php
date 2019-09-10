<?php

namespace Tailgate\Domain\Model\Team;

class TeamView
{
    private $teamId;
    private $designation;
    private $mascot;
    private $follows = [];

    public function __construct($teamId, $designation, $mascot)
    {
        $this->teamId = $teamId;
        $this->designation = $designation;
        $this->mascot = $mascot;
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

    public function getFollows()
    {
        return $this->follows;
    }

    public function addFollowViews($followView)
    {
        if (is_array($followView)) {
            $this->follows = $followView;
        } else {
            $this->follows[] = $followView;
        }
    }
}
