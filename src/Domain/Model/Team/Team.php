<?php

namespace Tailgate\Domain\Model\Team;

use Tailgate\Domain\Model\AbstractEntity;
use Buttercup\Protects\IdentifiesAggregate;
use Tailgate\Domain\Model\Group\GroupId;

class Team extends AbstractEntity
{
    private $teamId;
    private $designation;
    private $mascot;
    private $followers = [];

    protected function __construct(
        $teamId,
        $designation,
        $mascot
    ) {
        $this->teamId = $teamId;
        $this->designation = $designation;
        $this->mascot = $mascot;
    }

    public static function create(TeamId $teamId, $designation, $mascot)
    {
        $newTeam = new Team(
            $teamId,
            $designation,
            $mascot
        );

        $newTeam->recordThat(
            new TeamAdded(
                $teamId,
                $designation,
                $mascot
            )
        );

        return $newTeam;
    }

    protected static function createEmptyEntity(IdentifiesAggregate $teamId)
    {
        return new Team($teamId, '', '');
    }

    public function getId()
    {
        return (string) $this->teamId;
    }

    public function getDesignation()
    {
        return $this->designation;
    }

    public function getMascot()
    {
        return $this->mascot;
    }

    public function getFollowers()
    {
        return $this->followers;
    }

    public function update($designation, $mascot)
    {
        $this->applyAndRecordThat(
            new TeamUpdated(
                $this->teamId,
                $designation,
                $mascot
            )
        );
    }

    public function followTeam(GroupId $groupId)
    {
        $this->applyAndRecordThat(
            new TeamFollowed(
                $this->teamId,
                new FollowId(),
                $groupId
            )
        );
    }

    public function delete()
    {
        $this->applyAndRecordThat(
            new TeamDeleted($this->teamId)
        );
    }

    public function deleteFollow(FollowId $followId)
    {
        $this->applyAndRecordThat(
            new FollowDeleted(
                $this->teamId,
                $followId,
            )
        );
    }

    protected function applyTeamAdded(TeamAdded $event)
    {
        $this->designation = $event->getDesignation();
        $this->mascot = $event->getMascot();
    }

    protected function applyTeamUpdated(TeamUpdated $event)
    {
        $this->designation = $event->getDesignation();
        $this->mascot = $event->getMascot();
    }

    protected function applyTeamFollowed(TeamFollowed $event)
    {
        $this->followers[] = Follow::create(
            $event->getAggregateId(),
            $event->getFollowId(),
            $event->getGroupId()
        );
    }

    protected function applyFollowDeleted(FollowDeleted $event)
    {
        $this->followers = array_values(array_filter($this->followers, function ($follower) use ($event) {
            return !$follower->getFollowId()->equals($event->getFollowId());
        }));
    }

    protected function applyTeamDeleted(TeamDeleted $event)
    {
    }
}
