<?php

namespace Tailgate\Domain\Model\Group;

class GroupView
{
    private $groupId;
    private $name;
    private $ownerId;
    private $members = [];
    private $scores = [];

    public function __construct($groupId, $name, $ownerId)
    {
        $this->groupId = $groupId;
        $this->name = $name;
        $this->ownerId = $ownerId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getOwnerId()
    {
        return $this->ownerId;
    }

    public function getMembers()
    {
        return $this->members;
    }

    public function getScores()
    {
        return $this->scores;
    }

    public function addMemberViews($memberView)
    {
        if (is_array($memberView)) {
            $this->members = $memberView;
        } else {
            $this->members[] = $memberView;
        }
    }

    public function addScoreViews($scoreView)
    {
        if (is_array($scoreView)) {
            $this->scores = $scoreView;
        } else {
            $this->scores[] = $scoreView;
        }
    }
}