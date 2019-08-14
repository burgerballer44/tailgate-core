<?php

namespace Tailgate\Tests\Infrastructure\Persistence\ViewRepository\PDO;

use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\Group\ScoreId;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\ScoreViewRepository;

class PDOScoreViewRepositoryTest extends TestCase
{
    private $pdoMock;
    private $pdoStatementMock;
    private $viewRepository;

    public function setUp()
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
            ->with('SELECT * FROM `score` WHERE score_id = :score_id LIMIT 1')
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

        $this->expectException(\Exception::class);
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
            ->with('SELECT * FROM `score` WHERE score_id = :score_id LIMIT 1')
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
                'user_id' => 'blah',
                'game_id' => 'blah',
                'home_team_prediction' => 'blah',
                'away_team_prediction' => 'blah',
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
            ->with('SELECT * FROM `score` WHERE group_id = :group_id')
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

    public function testItCanGetAllScoresOfAUserInTheGroup()
    {
        $groupId = GroupId::fromString('groupId');
        $userId = UserId::fromString('userId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `score` WHERE group_id = :group_id AND user_id = :user_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':group_id' => (string) $groupId, ':user_id' => (string) $userId]);

         // fetch method called
         $this->pdoStatementMock
            ->expects($this->atLeastOnce())
            ->method('fetch');

        $this->viewRepository->getAllByGroupUser($groupId, $userId);
    }

    public function testItCanGetAllScoresOfAGameInTheGroup()
    {
        $groupId = GroupId::fromString('groupId');
        $gameId = GameId::fromString('gameId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `score` WHERE group_id = :group_id AND game_id = :game_id')
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
