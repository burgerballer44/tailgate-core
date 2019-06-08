<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\AbstractEntity;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\Game\GameId;
use Tailgate\Domain\Model\Team\TeamId;
use Buttercup\Protects\IdentifiesAggregate;

class Group extends AbstractEntity
{
    const G_ROLE_ADMIN = 10; // someone who can do manage the gorup
    const G_ROLE_MEMBER = 20; // regular user who can submit scores

    private $groupId;
    private $name;
    private $ownerId;
    private $scores = [];
    private $members = [];
    private $follows = [];

    protected function __construct($groupId, $name, $ownerId)
    {
        $this->groupId = $groupId;
        $this->name = $name;
        $this->ownerId = $ownerId;
    }

    public static function create(GroupId $groupId, $name, UserId $ownerId)
    {
        $newGroup = new Group($groupId, $name, $ownerId);

        $newGroup->recordThat(
            new GroupCreated($groupId, $name, $ownerId)
        );

        $newGroup->applyAndRecordThat(
            new MemberAdded(
                new MemberId(),
                $groupId,
                $ownerId,
                Group::G_ROLE_ADMIN
            )
        );

        return $newGroup;
    }

    protected static function createEmptyEntity(IdentifiesAggregate $groupId)
    {
        return new Group($groupId, '', '');
    }

    public function getId()
    {
        return (string) $this->groupId;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getOwnerId()
    {
        return (string) $this->ownerId;
    }

    public function getScores()
    {
        return $this->scores;
    }

    public function getMembers()
    {
        return $this->members;
    }

    public function getFollows()
    {
        return $this->follows;
    }

    public function submitScore(GroupId $groupId, UserId $userId, GameId $gameId, $homeTeamPrediction, $awayTeamPrediction)
    {
        $this->applyAndRecordThat(
            new ScoreSubmitted(
                new ScoreId(),
                $groupId,
                $userId,
                $gameId,
                $homeTeamPrediction,
                $awayTeamPrediction
            )
        );
    }

    public function addMember(GroupId $groupId, UserId $userId)
    {
        $this->applyAndRecordThat(
            new MemberAdded(
                new MemberId(),
                $groupId,
                $userId,
                Group::G_ROLE_MEMBER
            )
        );
    }

    public function followTeam(GroupId $groupId, TeamId $teamId)
    {
        $this->applyAndRecordThat(
             new TeamFollowed(
                new FollowId(),
                $groupId,
                $teamId
            )
        );
    }

    protected function applyGroupCreated(GroupCreated $event)
    {
        $this->name = $event->getName();
        $this->ownerId = $event->getOwnerId();
    }

    protected function applyScoreSubmitted(ScoreSubmitted $event)
    {
        $this->scores[] = Score::create(
            $event->getScoreId(),
            $event->getGroupId(),
            $event->getUserId(),
            $event->getGameId(),
            $event->getHomeTeamPrediction(),
            $event->getAwayTeamPrediction(),
            $event->getOccurredOn()
        );
    }

    protected function applyMemberAdded(MemberAdded $event)
    {
        $this->members[] = Member::create(
            $event->getMemberId(),
            $event->getGroupId(),
            $event->getUserId(),
            $event->getGroupRole()
        );
    }

    protected function applyTeamFollowed(TeamFollowed $event)
    {
        $this->follows[] = Follow::create(
            $this->followId = $event->getFollowId(),
            $this->groupId = $event->getGroupId(),
            $this->teamId = $event->getTeamId()
        );
    }
}
