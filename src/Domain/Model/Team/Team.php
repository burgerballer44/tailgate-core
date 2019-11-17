<?php

namespace Tailgate\Domain\Model\Team;

use Buttercup\Protects\IdentifiesAggregate;
use Tailgate\Domain\Model\AbstractEntity;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\ModelException;

class Team extends AbstractEntity
{
    private $teamId;
    private $designation;
    private $mascot;
    private $follows = [];

    protected function __construct($teamId, $designation, $mascot)
    {
        $this->teamId = $teamId;
        $this->designation = $designation;
        $this->mascot = $mascot;
    }

    /**
     * create a team
     * @param  TeamId $teamId      [description]
     * @param  [type] $designation [description]
     * @param  [type] $mascot      [description]
     * @return [type]              [description]
     */
    public static function create(TeamId $teamId, $designation, $mascot)
    {
        $newTeam = new Team($teamId, $designation, $mascot);

        $newTeam->recordThat(new TeamAdded($teamId, $designation, $mascot));

        return $newTeam;
    }

    /**
     * create an empty team
     * @param  IdentifiesAggregate $teamId [description]
     * @return [type]                      [description]
     */
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

    public function getFollows()
    {
        return $this->follows;
    }

    /**
     * updates the team's designation adn mascot
     * @param  [type] $designation [description]
     * @param  [type] $mascot      [description]
     * @return [type]              [description]
     */
    public function update($designation, $mascot)
    {
        $this->applyAndRecordThat(new TeamUpdated($this->teamId, $designation, $mascot));
    }

    /**
     * have a group follow a team
     * @param  GroupId $groupId [description]
     * @return [type]           [description]
     */
    public function followTeam(GroupId $groupId, SeasonId $seasonId)
    {
        if ($team = $this->getFollowByGroupId($groupId)) {
            throw new ModelException('The group already follows this team.');
        }

        $this->applyAndRecordThat(new TeamFollowed($this->teamId, new FollowId(), $groupId, $seasonId));
    }

    /**
     * delete all follows the team
     * @return [type] [description]
     */
    public function delete()
    {
        $this->applyAndRecordThat(new TeamDeleted($this->teamId));
    }

    /**
     * remove a follow
     * @param  FollowId $followId [description]
     * @return [type]             [description]
     */
    public function deleteFollow(FollowId $followId)
    {
        if (!$follow = $this->getFollowById($followId)) {
            throw new ModelException('The follow does not exist.');
        }

        $this->applyAndRecordThat(new FollowDeleted($this->teamId, $followId));
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
        $this->follows[] = Follow::create(
            $event->getAggregateId(),
            $event->getFollowId(),
            $event->getGroupId(),
            $event->getSeasonId()
        );
    }

    protected function applyFollowDeleted(FollowDeleted $event)
    {
        $this->follows = array_values(array_filter($this->follows, function ($follow) use ($event) {
            return !$follow->getFollowId()->equals($event->getFollowId());
        }));
    }

    protected function applyTeamDeleted(TeamDeleted $event)
    {
        $this->follows = [];
    }

    private function getFollowByGroupId(GroupId $groupId)
    {
        foreach ($this->follows as $follow) {
            if ($follow->getGroupId()->equals($groupId)) {
                return $follow;
            }
        }
    }

    private function getFollowById(FollowId $followId)
    {
        foreach ($this->follows as $follow) {
            if ($follow->getFollowId()->equals($followId)) {
                return $follow;
            }
        }
    }
}
