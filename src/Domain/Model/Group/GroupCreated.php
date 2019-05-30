<?php

namespace Tailgate\Domain\Model\Group;

use Buttercup\Protects\DomainEvent;

class GroupCreated implements DomainEvent
{
    private $groupId;
    private $name;

    public function __construct(GroupId $groupId, $name)
    {
        $this->groupId = $groupId;
        $this->name = $name;
    }

    public function getAggregateId()
    {
        return $this->groupId;
    }

    public function getName()
    {
        return $this->name;
    }
}