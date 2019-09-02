<?php

namespace Tailgate\Infrastructure\Persistence\Projection\PDO;

use PDO;
use Tailgate\Domain\Model\Group\GroupCreated;
use Tailgate\Domain\Model\Group\GroupProjectionInterface;
use Tailgate\Domain\Model\Group\MemberAdded;
use Tailgate\Domain\Model\Group\ScoreSubmitted;
use Tailgate\Domain\Model\Group\GroupUpdated;
use Tailgate\Domain\Model\Group\MemberDeleted;
use Tailgate\Domain\Model\Group\ScoreDeleted;
use Tailgate\Domain\Model\Group\GroupScoreUpdated;
use Tailgate\Domain\Model\Group\GroupDeleted;
use Tailgate\Domain\Model\Group\PlayerAdded;
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
            'INSERT INTO `group` (group_id, name, owner_id, created_at)
            VALUES (:group_id, :name, :owner_id, :created_at)'
        );

        $stmt->execute([
            ':group_id' => $event->getAggregateId(),
            ':name' => $event->getName(),
            ':owner_id' => $event->getOwnerId(),
            ':created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s')
        ]);
    }

    public function projectMemberAdded(MemberAdded $event)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO `member` (member_id, group_id, user_id, role, created_at)
            VALUES (:member_id, :group_id, :user_id, :role, :created_at)'
        );

        $stmt->execute([
            ':group_id' => $event->getAggregateId(),
            ':member_id' => $event->getMemberId(),
            ':user_id' => $event->getUserId(),
            ':role' => $event->getGroupRole(),
            ':created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s')
        ]);
    }

    public function projectScoreSubmitted(ScoreSubmitted $event)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO `score` (score_id, group_id, user_id, game_id, home_team_prediction, away_team_prediction, created_at)
            VALUES (:score_id, :group_id, :user_id, :game_id, :home_team_prediction, :away_team_prediction, :created_at)'
        );

        $stmt->execute([
            ':score_id' => $event->getScoreId(),
            ':group_id' => $event->getAggregateId(),
            ':user_id' => $event->getUserId(),
            ':game_id' => $event->getGameId(),
            ':home_team_prediction' => $event->getHomeTeamPrediction(),
            ':away_team_prediction' => $event->getAwayTeamPrediction(),
            ':created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s')
        ]);
    }

    public function projectGroupUpdated(GroupUpdated $event)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE `group` (name, owner_id)
            VALUES (:name, :owner_id)
            WHERE :group_id = group_id'
        );

        $stmt->execute([
            ':group_id' => $event->getAggregateId(),
            ':name' => $event->getName(),
            ':owner_id' => $event->getOwnerId(),
        ]);
    }

    public function projectMemberDeleted(MemberDeleted $event)
    {
        $stmt = $this->pdo->prepare('DELETE FROM `member` WHERE :member_id = member_id');

        $stmt->execute([':member_id' => $event->getMemberId()]);
    }

    public function projectScoreDeleted(ScoreDeleted $event)
    {
        $stmt = $this->pdo->prepare('DELETE FROM `score` WHERE :score_id = score_id');

        $stmt->execute([':score_id' => $event->getScoreId()]);
    }

    public function projectGroupDeleted(GroupDeleted $event)
    {
        $stmt = $this->pdo->prepare('DELETE FROM `score` WHERE :group_id = group_id');
        $stmt->execute([':group_id' => $event->getAggregateId()]);

        $stmt = $this->pdo->prepare('DELETE FROM `member` WHERE :group_id = group_id');
        $stmt->execute([':group_id' => $event->getAggregateId()]);

        $stmt = $this->pdo->prepare('DELETE FROM `group` WHERE :group_id = group_id');
        $stmt->execute([':group_id' => $event->getAggregateId()]);
    }

    public function projectGroupScoreUpdated(GroupScoreUpdated $event)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE `score` (home_team_prediction, away_team_prediction)
            VALUES (:home_team_prediction, :away_team_prediction)
            WHERE :score_id = score_id'
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
            ':created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s')
        ]);
    }
}
