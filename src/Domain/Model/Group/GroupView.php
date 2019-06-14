<?php

namespace Tailgate\Domain\Model\Group;

class GroupView
{
    private $groupId;
    private $name;
    private $ownerId;

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
}