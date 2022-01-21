<?php

namespace Tailgate\Domain\Model\Group;

use Burger\Aggregate\IdentifiesAggregate;
use RuntimeException;
use Tailgate\Domain\Model\AbstractEventBasedEntity;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Group\GroupInviteCode;
use Tailgate\Domain\Model\Group\GroupRole;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\User\UserId;

class Group extends AbstractEventBasedEntity
{
    const SINGLE_PLAYER = 0;  // member can not add multiple players to group
    const MULTIPLE_PLAYERS = 1;  // member can add multiple players to group

    const MEMBER_LIMIT = 30;  // maximum number of members in a group

    const PLAYER_LIMIT = 5;  // maximum number of players for a player who can have multiple

    const MIN_NUMBER_ADMINS = 1;  // minimum number of admins that have to be in a group

    private $groupId;
    private $name;
    private $inviteCode;
    private $ownerId;
    private $scores = [];
    private $members = [];
    private $players = [];
    private $follow;

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getInviteCode()
    {
        return $this->inviteCode;
    }

    public function getOwnerId()
    {
        return $this->ownerId;
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

    public function getFollow()
    {
        return $this->follow;
    }

    // create an empty group
    protected static function createEmptyEntity(IdentifiesAggregate $groupId)
    {
        return new Group($groupId, '', '', '');
    }

    // create a group
    public static function create(GroupId $groupId, $name, GroupInviteCode $inviteCode, UserId $ownerId, Date $dateOccurred)
    {
        $group = new static();

        $group->applyAndRecordThat(new GroupCreated($groupId, $name, $inviteCode, $ownerId, $dateOccurred));

        $group->applyAndRecordThat(
            new MemberAdded($groupId, new MemberId(), $ownerId, GroupRole::getGroupAdmin(), Group::MULTIPLE_PLAYERS, $dateOccurred)
        );

        return $group;
    }

    // add a score
    public function submitScore(PlayerId $playerId, GameId $gameId, $homeTeamPrediction, $awayTeamPrediction, Date $dateOccurred)
    {
        if (!$this->getPlayerById($playerId)) {
            throw new RuntimeException('The player submitting the score does not exist.');
        }

        if ($this->getScoreByPlayerIdAndGameId($playerId, $gameId)) {
            throw new RuntimeException('The player already submitted a score for this game.');
        }

        $this->applyAndRecordThat(
            new ScoreSubmitted(
                $this->groupId,
                new ScoreId(),
                $playerId,
                $gameId,
                $homeTeamPrediction,
                $awayTeamPrediction,
                $dateOccurred
            )
        );
    }

    // add a player
    public function addPlayer(MemberId $memberId, $username, Date $dateOccurred)
    {
        if (!$member = $this->getMemberById($memberId)) {
            throw new RuntimeException('The member does not exist. Cannot add the player.');
        }

        $playerCount = count($this->getPlayersByMemberId($memberId));

        // singleplayers should only have one
        if ($member->getAllowMultiplePlayers() == self::SINGLE_PLAYER && $playerCount) {
            throw new RuntimeException('Player limit reached for member.');
        }

        // multipleplayers cannot have more than the limit
        if ($playerCount >= self::PLAYER_LIMIT) {
            throw new RuntimeException('Player limit reached for member.');
        }

        $this->applyAndRecordThat(
            new PlayerAdded($this->groupId, new PlayerId(), $memberId, $username, $dateOccurred)
        );
    }

    // change who owns a player to a different member
    public function changePlayerOwner(PlayerId $playerId, MemberId $memberId, Date $dateOccurred)
    {
        if (!$this->getMemberById($memberId)) {
            throw new RuntimeException('The member does not exist. Cannot change the player owner.');
        }

        if (!$this->getPlayerById($playerId)) {
            throw new RuntimeException('The player does not exist. Cannot change the player owner.');
        }

        $this->applyAndRecordThat(
            new PlayerOwnerChanged($this->groupId, $playerId, $memberId, $dateOccurred)
        );
    }

    // changes a score to something else
    public function updateScore(ScoreId $scoreId, $homeTeamPrediction, $awayTeamPrediction, Date $dateOccurred)
    {
        if (!$this->getScoreById($scoreId)) {
            throw new RuntimeException('The score does not exist. Cannot update the score.');
        }

        $this->applyAndRecordThat(
            new GroupScoreUpdated($this->groupId, $scoreId, $homeTeamPrediction, $awayTeamPrediction, $dateOccurred)
        );
    }

    // add a member
    public function addMember(UserId $userId, Date $dateOccurred)
    {
        if ($this->getMemberByUserId($userId)) {
            throw new RuntimeException('The member is already in the group.');
        }

        if (count($this->members) >= self::MEMBER_LIMIT) {
            throw new RuntimeException('Group member limit reached.');
        }

        $this->applyAndRecordThat(
            new MemberAdded($this->groupId, new MemberId(), $userId, GroupRole::getGroupMember(), Group::SINGLE_PLAYER, $dateOccurred)
        );
    }

    // updates the group name, and owner
    public function update($name, UserId $ownerId, Date $dateOccurred)
    {
        $this->applyAndRecordThat(
            new GroupUpdated($this->groupId, $name, $ownerId, $dateOccurred)
        );
    }

    // updates a member role, and if they can add multiple players
    public function updateMember(MemberId $memberId, $groupRole, $allowMultiple, Date $dateOccurred)
    {
        if (!$this->getMemberById($memberId)) {
            throw new RuntimeException('The member does not exist.');
        }

        // get all admin members
        $adminMembers = $this->getMembersThatAreAdmin();

        // get their member ids
        $adminMemberIds = array_map(function ($member) {
            return (string) $member->getMemberId();
        }, $adminMembers);

        // if there is only one admin
        // and that admin is the member we are trying to update
        // and we are updating away from being an admin
        if (
            count($adminMemberIds) == self::MIN_NUMBER_ADMINS
            && in_array($memberId, $adminMemberIds)
            && ($groupRole != GroupRole::getGroupAdmin())
        ) {
            throw new RuntimeException('Cannot change the last admin in the group to not be an admin.');
        }

        // set single player by default. no need for exception
        if (!in_array($allowMultiple, [self::SINGLE_PLAYER, self::MULTIPLE_PLAYERS])) {
            $allowMultiple = self::SINGLE_PLAYER;
        }

        $this->applyAndRecordThat(
            new MemberUpdated($this->groupId, $memberId, $groupRole, $allowMultiple, $dateOccurred)
        );
    }

    // remove all members, players, and scores from a group
    public function delete(Date $dateOccurred)
    {
        $this->applyAndRecordThat(new GroupDeleted($this->groupId, $dateOccurred));
    }

    // delete a member, all players the member has, and all scores from each player
    public function deleteMember(MemberId $memberId, Date $dateOccurred)
    {
        if (!$this->getMemberById($memberId)) {
            throw new RuntimeException('The member does not exist.');
        }

        // get all admin members
        $adminMembers = $this->getMembersThatAreAdmin();

        // get their member ids
        $adminMemberIds = array_map(function ($member) {
            return (string) $member->getMemberId();
        }, $adminMembers);

        // if there is only one admin and that admin is the member we are trying to delete
        if (
            count($adminMemberIds) == self::MIN_NUMBER_ADMINS
            && in_array($memberId, $adminMemberIds)
        ) {
            throw new RuntimeException('Cannot remove the last admin in a group.');
        }

        $this->applyAndRecordThat(
            new MemberDeleted($this->groupId, $memberId, $dateOccurred)
        );
    }

    // delete a player, and all of their scores
    public function deletePlayer(PlayerId $playerId, Date $dateOccurred)
    {
        if (!$this->getPlayerById($playerId)) {
            throw new RuntimeException('The player does not exist.');
        }

        $this->applyAndRecordThat(
            new PlayerDeleted($this->groupId, $playerId, $dateOccurred)
        );
    }

    // delete a score
    public function deleteScore(ScoreId $scoreId, Date $dateOccurred)
    {
        if (!$this->getScoreById($scoreId)) {
            throw new RuntimeException('The score does not exist. Cannot update the score.');
        }

        $this->applyAndRecordThat(
            new ScoreDeleted($this->groupId, $scoreId, $dateOccurred)
        );
    }

    // follow a team
    public function followTeam(TeamId $teamId, SeasonId $seasonId, Date $dateOccurred)
    {
        if ($this->follow) {
            throw new RuntimeException('Cannot follow this team. This group is already following a team.');
        }

        $this->applyAndRecordThat(new TeamFollowed($this->groupId, new FollowId(), $teamId, $seasonId, $dateOccurred));
    }

    // remove a follow
    public function deleteFollow(FollowId $followId, Date $dateOccurred)
    {
        if (!$this->follow instanceof Follow) {
            throw new RuntimeException('Group is not following a team.');
        }

        $this->applyAndRecordThat(new FollowDeleted($this->groupId, $followId, $dateOccurred));
    }

    protected function applyGroupCreated(GroupCreated $event)
    {
        $this->groupId = $event->getAggregateId();
        $this->name = $event->getName();
        $this->inviteCode = $event->getInviteCode();
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
        $this->players = [];
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

    protected function applyMemberDeleted(MemberDeleted $event)
    {
        $this->members = array_values(array_filter($this->members, function ($member) use ($event) {
            return !$member->getMemberId()->equals($event->getMemberId());
        }));

        $playerIdsForMember = array_map(function ($player) {
            return (string)$player->getPlayerId();
        }, $this->getPlayersByMemberId($event->getMemberId()));

        $this->players = array_values(array_filter($this->players, function ($player) use ($event) {
            return !$player->getMemberId()->equals($event->getMemberId());
        }));

        $this->scores = array_values(array_filter($this->scores, function ($score) use ($playerIdsForMember) {
            return !in_array((string)$score->getPlayerId(), $playerIdsForMember);
        }));
    }

    protected function applyPlayerDeleted(PlayerDeleted $event)
    {
        $this->players = array_values(array_filter($this->players, function ($player) use ($event) {
            return !$player->getPlayerId()->equals($event->getPlayerId());
        }));
        
        $this->scores = array_values(array_filter($this->scores, function ($score) use ($event) {
            return !$score->getPlayerId()->equals($event->getPlayerId());
        }));
    }

    protected function applyScoreDeleted(ScoreDeleted $event)
    {
        $this->scores = array_values(array_filter($this->scores, function ($score) use ($event) {
            return !$score->getScoreId()->equals($event->getScoreId());
        }));
    }

    protected function applyTeamFollowed(TeamFollowed $event)
    {
        $this->follow = Follow::create(
            $event->getAggregateId(),
            $event->getFollowId(),
            $event->getTeamId(),
            $event->getSeasonId()
        );
    }

    protected function applyPlayerOwnerChanged(PlayerOwnerChanged $event)
    {
        $player = $this->getPlayerById($event->getPlayerId());
        $player->changeMember($event->getMemberId());
    }

    protected function applyFollowDeleted(FollowDeleted $event)
    {
        $this->follow = null;
    }

    private function getMemberByUserId(UserId $userId)
    {
        foreach ($this->members as $member) {
            if ($member->getUserId()->equals($userId)) {
                return $member;
            }
        }
    }

    private function getPlayersByMemberId(MemberId $memberId)
    {
        return array_filter($this->players, function ($player) use ($memberId) {
            return $player->getMemberId()->equals($memberId);
        });
    }

    private function getMemberById(MemberId $memberId)
    {
        foreach ($this->members as $member) {
            if ($member->getMemberId()->equals($memberId)) {
                return $member;
            }
        }
    }

    private function getPlayerById(PlayerId $playerId)
    {
        foreach ($this->players as $player) {
            if ($player->getPlayerId()->equals($playerId)) {
                return $player;
            }
        }
    }

    private function getScoreById(ScoreId $scoreId)
    {
        foreach ($this->scores as $score) {
            if ($score->getScoreId()->equals($scoreId)) {
                return $score;
            }
        }
    }

    private function getScoreByPlayerIdAndGameId(PlayerId $playerId, GameId $gameId)
    {
        foreach ($this->scores as $score) {
            if ($score->getPlayerId()->equals($playerId) && $score->getGameId()->equals($gameId)) {
                return $score;
            }
        }
    }

    private function getMembersThatAreAdmin()
    {
        return array_filter($this->members, function ($member) {
            return $member->getGroupRole() == GroupRole::getGroupAdmin();
        });
    }

}
