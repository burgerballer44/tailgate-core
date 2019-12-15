<?php

namespace Tailgate\Infrastructure\Persistence\Projection\PDO;

use PDO;
use Tailgate\Domain\Model\Group\GroupCreated;
use Tailgate\Domain\Model\Group\GroupDeleted;
use Tailgate\Domain\Model\Group\GroupProjectionInterface;
use Tailgate\Domain\Model\Group\GroupScoreUpdated;
use Tailgate\Domain\Model\Group\GroupUpdated;
use Tailgate\Domain\Model\Group\MemberAdded;
use Tailgate\Domain\Model\Group\MemberDeleted;
use Tailgate\Domain\Model\Group\MemberUpdated;
use Tailgate\Domain\Model\Group\PlayerAdded;
use Tailgate\Domain\Model\Group\PlayerDeleted;
use Tailgate\Domain\Model\Group\TeamFollowed;
use Tailgate\Domain\Model\Group\FollowDeleted;
use Tailgate\Domain\Model\Group\ScoreDeleted;
use Tailgate\Domain\Model\Group\ScoreSubmitted;
use Tailgate\Infrastructure\Persistence\Projection\AbstractProjection;

class GroupProjection extends AbstractProjection implements GroupProjectionInterface
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function projectGroupCreated(GroupCreated $event)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO `group` (group_id, name, invite_code, owner_id, created_at)
            VALUES (:group_id, :name, :invite_code, :owner_id, :created_at)'
        );

        $stmt->execute([
            ':group_id' => $event->getAggregateId(),
            ':name' => $event->getName(),
            ':invite_code' => $event->getInviteCode(),
            ':owner_id' => $event->getOwnerId(),
            ':created_at' => (new \DateTimeImmutable())->format(self::DATE_FORMAT)
        ]);
    }

    public function projectMemberAdded(MemberAdded $event)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO `member` (member_id, group_id, user_id, role, allow_multiple, created_at)
            VALUES (:member_id, :group_id, :user_id, :role, :allow_multiple, :created_at)'
        );

        $stmt->execute([
            ':group_id' => $event->getAggregateId(),
            ':member_id' => $event->getMemberId(),
            ':user_id' => $event->getUserId(),
            ':role' => $event->getGroupRole(),
            ':allow_multiple' => $event->getAllowMultiplePlayers(),
            ':created_at' => (new \DateTimeImmutable())->format(self::DATE_FORMAT)
        ]);
    }

    public function projectScoreSubmitted(ScoreSubmitted $event)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO `score` (score_id, group_id, player_id, member_id, game_id, home_team_prediction, away_team_prediction, created_at)
            VALUES (:score_id, :group_id, :player_id, :member_id, :game_id, :home_team_prediction, :away_team_prediction, :created_at)'
        );

        $stmt->execute([
            ':score_id' => $event->getScoreId(),
            ':group_id' => $event->getAggregateId(),
            ':player_id' => $event->getPlayerId(),
            ':member_id' => $event->getMemberId(),
            ':game_id' => $event->getGameId(),
            ':home_team_prediction' => $event->getHomeTeamPrediction(),
            ':away_team_prediction' => $event->getAwayTeamPrediction(),
            ':created_at' => (new \DateTimeImmutable())->format(self::DATE_FORMAT)
        ]);
    }

    public function projectGroupUpdated(GroupUpdated $event)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE `group` SET name = :name, owner_id = :owner_id
            WHERE group_id = :group_id'
        );

        $stmt->execute([
            ':group_id' => $event->getAggregateId(),
            ':name' => $event->getName(),
            ':owner_id' => $event->getOwnerId(),
        ]);
    }

    public function projectMemberDeleted(MemberDeleted $event)
    {
        $stmt = $this->pdo->prepare('DELETE FROM `score` WHERE member_id = :member_id');
        $stmt->execute([':member_id' => $event->getMemberId()]);

        $stmt = $this->pdo->prepare('DELETE FROM `player` WHERE member_id = :member_id');
        $stmt->execute([':member_id' => $event->getMemberId()]);

        $stmt = $this->pdo->prepare('DELETE FROM `member` WHERE member_id = :member_id');
        $stmt->execute([':member_id' => $event->getMemberId()]);
    }

    public function projectPlayerDeleted(PlayerDeleted $event)
    {
        $stmt = $this->pdo->prepare('DELETE FROM `score` WHERE player_id = :player_id');
        $stmt->execute([':player_id' => $event->getPlayerId()]);

        $stmt = $this->pdo->prepare('DELETE FROM `player` WHERE player_id = :player_id');
        $stmt->execute([':player_id' => $event->getPlayerId()]);
    }

    public function projectScoreDeleted(ScoreDeleted $event)
    {
        $stmt = $this->pdo->prepare('DELETE FROM `score` WHERE score_id = :score_id');
        $stmt->execute([':score_id' => $event->getScoreId()]);
    }

    public function projectGroupDeleted(GroupDeleted $event)
    {
        $stmt = $this->pdo->prepare('DELETE FROM `score` WHERE group_id = :group_id');
        $stmt->execute([':group_id' => $event->getAggregateId()]);

        $stmt = $this->pdo->prepare('DELETE FROM `player` WHERE group_id = :group_id');
        $stmt->execute([':group_id' => $event->getAggregateId()]);

        $stmt = $this->pdo->prepare('DELETE FROM `member` WHERE group_id = :group_id');
        $stmt->execute([':group_id' => $event->getAggregateId()]);

        $stmt = $this->pdo->prepare('DELETE FROM `follow` WHERE group_id = :group_id');
        $stmt->execute([':group_id' => $event->getAggregateId()]);

        $stmt = $this->pdo->prepare('DELETE FROM `group` WHERE group_id = :group_id');
        $stmt->execute([':group_id' => $event->getAggregateId()]);
    }

    public function projectMemberUpdated(MemberUpdated $event)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE `member` SET member_id = :member_id, role =:role, allow_multiple = :allow_multiple
            WHERE member_id = :member_id'
        );

        $stmt->execute([
            ':member_id' => $event->getMemberId(),
            ':role' => $event->getGroupRole(),
            ':allow_multiple' => $event->getAllowMultiplePlayers()
        ]);
    }

    public function projectGroupScoreUpdated(GroupScoreUpdated $event)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE `score` SET home_team_prediction = :home_team_prediction, away_team_prediction = :away_team_prediction
            WHERE score_id = :score_id'
        );

        $stmt->execute([
            ':score_id' => $event->getScoreId(),
            ':home_team_prediction' => $event->getHomeTeamPrediction(),
            ':away_team_prediction' => $event->getAwayTeamPrediction(),
        ]);
    }

    public function projectPlayerAdded(PlayerAdded $event)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO `player` (player_id, member_id, group_id, username, created_at)
            VALUES (:player_id, :member_id, :group_id, :username, :created_at)'
        );

        $stmt->execute([
            ':group_id' => $event->getAggregateId(),
            ':player_id' => $event->getPlayerId(),
            ':member_id' => $event->getMemberId(),
            ':username' => $event->getUsername(),
            ':created_at' => (new \DateTimeImmutable())->format(self::DATE_FORMAT)
        ]);
    }

    public function projectTeamFollowed(TeamFollowed $event)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO `follow` (follow_id, group_id, team_id, season_id, created_at)
            VALUES (:follow_id, :group_id, :team_id, :season_id, :created_at)'
        );

        $stmt->execute([
            ':follow_id' => $event->getFollowId(),
            ':group_id' => $event->getAggregateId(),
            ':season_id' => $event->getSeasonId(),
            ':team_id' => $event->getTeamId(),
            ':created_at' => (new \DateTimeImmutable())->format(self::DATE_FORMAT)
        ]);
    }

    public function projectFollowDeleted(FollowDeleted $event)
    {
        $stmt = $this->pdo->prepare('DELETE FROM `follow` WHERE group_id = :group_id');
        $stmt->execute([':group_id' => $event->getAggregateId()]);

        $stmt = $this->pdo->prepare('DELETE FROM `score` WHERE group_id = :group_id');
        $stmt->execute([':group_id' => $event->getAggregateId()]);
    }
}
