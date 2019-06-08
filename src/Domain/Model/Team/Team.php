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
    private $followers;

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

    public function followTeam(GroupId $groupId)
    {
        $this->applyAndRecordThat(
             new TeamFollowed(
                new FollowId(),
                $this->teamId,
                $groupId
            )
        );
    }

    protected function applyTeamAdded(TeamAdded $event)
    {
        $this->designation = $event->getDesignation();
        $this->mascot = $event->getMascot();
    }

    protected function applyTeamFollowed(TeamFollowed $event)
    {
        $this->followers[] = Follow::create(
            $this->followId = $event->getFollowId(),
            $this->teamId = $event->getTeamId(),
            $this->groupId = $event->getGroupId()
        );
    }
}
