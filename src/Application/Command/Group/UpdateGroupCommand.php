<?php

namespace Tailgate\Application\Command\Group;

class UpdateGroupCommand
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
