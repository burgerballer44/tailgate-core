<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\DomainEvent;

class FollowDeleted implements DomainEvent, GroupDomainEvent
{
    private $groupId;
    private $followId;
    private $dateOccurred;

    public function __construct(GroupId $groupId, FollowId $followId, Date $dateOccurred)
    {
        $this->groupId = $groupId;
        $this->followId = $followId;
        $this->dateOccurred = $dateOccurred;
    }

    public function getEventDescription(): string
    {
        return 'Group unfollowed a team.';
    }

    public function getAggregateId()
    {
        return $this->groupId;
    }

    public function getFollowId()
    {
        return $this->followId;
    }

    public function getDateOccurred()
    {
        return (string) $this->dateOccurred;
    }
}
