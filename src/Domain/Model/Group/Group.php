<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\AbstractEntity;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\Season\GameId;
use Buttercup\Protects\IdentifiesAggregate;

class Group extends AbstractEntity
{
    const G_ROLE_ADMIN = 'Group-Admin'; // someone who can do manage the gorup
    const G_ROLE_MEMBER = 'Group-Member'; // regular user who can submit scores

    const SINGLE_PLAYER = 0;  // member can not add multiple players to group
    const MULTIPLE_PLAYERS = 1;  // member can add multiple players to group

    private $groupId;
    private $name;
    private $ownerId;
    private $scores = [];
    private $members = [];
    private $players = [];

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
                $groupId,
                new MemberId(),
                $ownerId,
                Group::G_ROLE_ADMIN,
                Group::MULTIPLE_PLAYERS,
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

    public function getPlayers()
    {
        return $this->players;
    }

    public function delete()
    {
        $this->applyAndRecordThat(new GroupDeleted($this->groupId));
    }

    public function deleteScore(ScoreId $scoreId)
    {
        $this->applyAndRecordThat(
            new ScoreDeleted(
                $this->groupId,
                $scoreId,
            )
        );
    }

    public function deleteMember(MemberId $memberId)
    {
        $this->applyAndRecordThat(
            new MemberDeleted(
                $this->groupId,
                $memberId,
            )
        );
    }

    public function submitScore(PlayerId $playerId, GameId $gameId, $homeTeamPrediction, $awayTeamPrediction)
    {
        $this->applyAndRecordThat(
            new ScoreSubmitted(
                $this->groupId,
                new ScoreId(),
                $playerId,
                $gameId,
                $homeTeamPrediction,
                $awayTeamPrediction
            )
        );
    }

    public function addPlayer(MemberId $memberId, $username)
    {
        $this->applyAndRecordThat(
            new PlayerAdded(
                $this->groupId,
                new PlayerId(),
                $memberId,
                $username
            )
        );
    }

    public function updateScore(ScoreId $scoreId, $homeTeamPrediction, $awayTeamPrediction)
    {
        $this->applyAndRecordThat(
            new GroupScoreUpdated(
                $this->groupId,
                $scoreId,
                $homeTeamPrediction,
                $awayTeamPrediction
            )
        );
    }

    public function addMember(UserId $userId)
    {
        $this->applyAndRecordThat(
            new MemberAdded(
                $this->groupId,
                new MemberId(),
                $userId,
                Group::G_ROLE_MEMBER,
                Group::SINGLE_PLAYER,
            )
        );
    }

    public function update($name, UserId $ownerId)
    {
        $this->applyAndRecordThat(
            new GroupUpdated($this->groupId, $name, $ownerId)
        );
    }

    public function updateMember(MemberId $memberId, $groupRole, $allowMultiple)
    {
        $this->applyAndRecordThat(
            new MemberUpdated(
                $this->groupId,
                $memberId,
                $groupRole,
                $allowMultiple
            )
        );
    }

    protected function applyGroupCreated(GroupCreated $event)
    {
        $this->name = $event->getName();
        $this->ownerId = $event->getOwnerId();
    }

    protected function applyGroupUpdated(GroupUpdated $event)
    {
        $this->name = $event->getName();
        $this->ownerId = $event->getOwnerId();
    }

    protected function applyScoreSubmitted(ScoreSubmitted $event)
    {
        $this->scores[] = Score::create(
            $event->getAggregateId(),
            $event->getScoreId(),
            $event->getPlayerId(),
            $event->getGameId(),
            $event->getHomeTeamPrediction(),
            $event->getAwayTeamPrediction()
        );
    }

    protected function applyPlayerAdded(PlayerAdded $event)
    {
        $this->players[] = Player::create(
            $event->getAggregateId(),
            $event->getPlayerId(),
            $event->getMemberId(),
            $event->getUsername()
        );
    }

    protected function applyMemberAdded(MemberAdded $event)
    {
        $this->members[] = Member::create(
            $event->getAggregateId(),
            $event->getMemberId(),
            $event->getUserId(),
            $event->getGroupRole(),
            $event->getAllowMultiplePlayers()
        );
    }

    protected function applyGroupDeleted(GroupDeleted $event)
    {
        $this->members = [];
        $this->scores = [];
    }

    protected function applyMemberUpdated(MemberUpdated $event)
    {
        $member = $this->getMemberById($event->getMemberId());
        $member->updateGroupRole($event->getGroupRole());
        $member->updateAllowMultiplePlayers($event->getAllowMultiplePlayers());
    }

    protected function applyGroupScoreUpdated(GroupScoreUpdated $event)
    {
        $score = $this->getScoreById($event->getScoreId());
        $score->update($event->getHomeTeamPrediction(), $event->getAwayTeamPrediction());
    }

    private function getMemberById(MemberId $memberId)
    {
        foreach ($this->members as $member) {
            if ($member->getMemberId() == $memberId) {
                return $member;
            }
        }
        // Todo
        // throw notfoundexception
    }

    private function getScoreById(ScoreId $scoreId)
    {
        foreach ($this->scores as $score) {
            if ($score->getScoreId() == $scoreId) {
                return $score;
            }
        }
        // Todo
        // throw notfoundexception
    }

    protected function applyMemberDeleted(MemberDeleted $event)
    {
        $this->members = array_values(array_filter($this->members, function ($member) use ($event) {
            return !$member->getMemberId()->equals($event->getMemberId());
        }));
    }

    protected function applyScoreDeleted(ScoreDeleted $event)
    {
        $this->scores = array_values(array_filter($this->scores, function ($score) use ($event) {
            return !$score->getScoreId()->equals($event->getScoreId());
        }));
    }
}
