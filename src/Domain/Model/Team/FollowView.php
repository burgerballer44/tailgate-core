<?php

namespace Tailgate\Domain\Model\Team;

class FollowView
{
    private $teamId;
    private $followId;
    private $groupId;
    private $groupName;
    private $teamDesignation;
    private $teamMascot;

    public function __construct($teamId, $followId, $groupId, $name, $designation, $mascot)
    {
        $this->teamId = $teamId;
        $this->followId = $followId;
        $this->groupId = $groupId;
        $this->groupName = $name;
        $this->teamDesignation = $designation;
        $this->teamMascot = $mascot;
    }

    public function getTeamId()
    {
        return $this->teamId;
    }

    public function getFollowId()
    {
        return $this->followId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function getGroupName()
    {
        return $this->groupName;
    }

    public function getTeamDesignation()
    {
        return $this->teamDesignation;
    }

    public function getTeamMascot()
    {
        return $this->teamMascot;
    }
}
