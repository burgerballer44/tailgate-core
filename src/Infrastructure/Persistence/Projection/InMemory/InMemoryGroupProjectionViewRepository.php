<?php

namespace Tailgate\Infrastructure\Persistence\Projection\InMemory;

use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupCreated;
use Tailgate\Domain\Model\Group\ScoreSubmitted;
use Tailgate\Domain\Model\Group\GroupProjectionInterface;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Infrastructure\Persistence\Projection\AbstractProjection;

class InMemoryGroupProjectionViewRepository extends AbstractProjection implements GroupProjectionInterface, GroupViewRepositoryInterface
{
    private $groups = [];

    public function get(groupId $groupId)
    {
        if (!isset($this->groups[(string) $groupId])) {
            return;
        }
        return $this->groups[(string) $groupId];
    }

    public function all()
    {
        return $this->groups;
    }

    public function projectGroupCreated(GroupCreated $event)
    {
        $this->groups[(string) $event->getAggregateId()] = [];
        $this->groups[(string) $event->getAggregateId()][] = $event;
    }
    public function projectScoreSubmitted(ScoreSubmitted $event)
    {
        $this->groups[(string) $event->getAggregateId()][] = $event;
    }
}