<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\DomainEvent;

class ScoreDeleted implements DomainEvent, GroupDomainEvent
{
    private $groupId;
    private $scoreId;
    private $dateOccurred;

    public function __construct(GroupId $groupId, ScoreId $scoreId, Date $dateOccurred)
    {
        $this->groupId = $groupId;
        $this->scoreId = $scoreId;
        $this->dateOccurred = $dateOccurred;
    }

    public function getEventDescription(): string
    {
        return 'Group score deleted.';
    }

    public function getAggregateId()
    {
        return $this->groupId;
    }

    public function getScoreId()
    {
        return $this->scoreId;
    }

    public function getDateOccurred()
    {
        return (string) $this->dateOccurred;
    }
}
