<?php

namespace Tailgate\Tests\Infrastructure\Persistence\ViewRepository\PDO;

use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\PlayerId;
use Tailgate\Domain\Model\Group\ScoreId;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\ScoreViewRepository;
use RuntimeException;

class PDOScoreViewRepositoryTest extends TestCase
{
    private $pdoMock;
    private $pdoStatementMock;
    private $viewRepository;

    public function setUp(): void
    {
        $this->pdoMock = $this->createMock(\PDO::class);
        $this->pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->viewRepository = new ScoreViewRepository($this->pdoMock);
    }

    public function testScoreThatDoesNotExistReturnsException()
    {
        $scoreId = ScoreId::fromString('scoreId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT s.score_id, s.group_id, s.player_id, s.game_id, s.home_team_prediction, s.away_team_prediction, g.home_team_id, g.away_team_id, hot.designation as home_designation, hot.mascot as home_mascot, awt.designation as away_designation, awt.mascot as away_mascot, p.username
            FROM `score` s
            JOIN `game` g on g.game_id = s.game_id
            JOIN `team` hot on hot.team_id = g.home_team_id
            JOIN `team` awt on awt.team_id = g.away_team_id
            JOIN `player` p on s.player_id = p.player_id
            WHERE s.score_id = :score_id LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute and fetch method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':score_id' => (string) $scoreId]);
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn(false);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Score not found.');
        $this->viewRepository->get($scoreId);
    }

    public function testItCanGetAScore()
    {
        $scoreId = ScoreId::fromString('scoreId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT s.score_id, s.group_id, s.player_id, s.game_id, s.home_team_prediction, s.away_team_prediction, g.home_team_id, g.away_team_id, hot.designation as home_designation, hot.mascot as home_mascot, awt.designation as away_designation, awt.mascot as away_mascot, p.username
            FROM `score` s
            JOIN `game` g on g.game_id = s.game_id
            JOIN `team` hot on hot.team_id = g.home_team_id
            JOIN `team` awt on awt.team_id = g.away_team_id
            JOIN `player` p on s.player_id = p.player_id
            WHERE s.score_id = :score_id LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':score_id' => (string) $scoreId]);
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn([
                'score_id' => 'blah',
                'group_id' => 'blah',
                'player_id' => 'blah',
                'game_id' => 'blah',
                'home_team_prediction' => 'blah',
                'away_team_prediction' => 'blah',
                'home_team_id' => 'blah',
                'away_team_id' => 'blah',
                'home_designation' => 'blah',
                'home_mascot' => 'blah',
                'away_designation' => 'blah',
                'away_mascot' => 'blah',
                'username' => 'blah',
            ]);

        $this->viewRepository->get($scoreId);
    }

    public function testItCanGetAllScoresOfAGroup()
    {
        $groupId = GroupId::fromString('groupId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT s.score_id, s.group_id, s.player_id, s.game_id, s.home_team_prediction, s.away_team_prediction, g.home_team_id, g.away_team_id, hot.designation as home_designation, hot.mascot as home_mascot, awt.designation as away_designation, awt.mascot as away_mascot, p.username
            FROM `score` s
            JOIN `game` g on g.game_id = s.game_id
            JOIN `team` hot on hot.team_id = g.home_team_id
            JOIN `team` awt on awt.team_id = g.away_team_id
            JOIN `player` p on s.player_id = p.player_id
            WHERE s.group_id = :group_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':group_id' => (string) $groupId]);

        // fetch method called
        $this->pdoStatementMock
            ->expects($this->atLeastOnce())
            ->method('fetch');

        $this->viewRepository->getAllByGroup($groupId);
    }

    public function testItCanGetAllScoresOfAPlayersInTheGroup()
    {
        $groupId = GroupId::fromString('groupId');
        $playerId = PlayerId::fromString('playerId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT s.score_id, s.group_id, s.player_id, s.game_id, s.home_team_prediction, s.away_team_prediction, g.home_team_id, g.away_team_id, hot.designation as home_designation, hot.mascot as home_mascot, awt.designation as away_designation, awt.mascot as away_mascot, p.username
            FROM `score` s
            JOIN `game` g on g.game_id = s.game_id
            JOIN `team` hot on hot.team_id = g.home_team_id
            JOIN `team` awt on awt.team_id = g.away_team_id
            JOIN `player` p on s.player_id = p.player_id
            WHERE s.group_id = :group_id AND s.player_id = :player_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':group_id' => (string) $groupId, ':player_id' => (string) $playerId]);

        // fetch method called
        $this->pdoStatementMock
            ->expects($this->atLeastOnce())
            ->method('fetch');

        $this->viewRepository->getAllByGroupPlayer($groupId, $playerId);
    }

    public function testItCanGetAllScoresOfAGameInTheGroup()
    {
        $groupId = GroupId::fromString('groupId');
        $gameId = GameId::fromString('gameId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT s.score_id, s.group_id, s.player_id, s.game_id, s.home_team_prediction, s.away_team_prediction, g.home_team_id, g.away_team_id, hot.designation as home_designation, hot.mascot as home_mascot, awt.designation as away_designation, awt.mascot as away_mascot, p.username
            FROM `score` s
            JOIN `game` g on g.game_id = s.game_id
            JOIN `team` hot on hot.team_id = g.home_team_id
            JOIN `team` awt on awt.team_id = g.away_team_id
            JOIN `player` p on s.player_id = p.player_id
            WHERE s.group_id = :group_id AND s.game_id = :game_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':group_id' => (string) $groupId, ':game_id' => (string) $gameId]);

        // fetch method called
        $this->pdoStatementMock
            ->expects($this->atLeastOnce())
            ->method('fetch');

        $this->viewRepository->getAllByGroupGame($groupId, $gameId);
    }
}
