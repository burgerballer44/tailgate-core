<?php

namespace Tailgate\Tests\Infrastructure\Persistence\ViewRepository\PDO;

use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\FollowId;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\FollowViewRepository;
use Tailgate\Infrastructure\Persistence\ViewRepository\RepositoryException;

class PDOFollowViewRepositoryTest extends TestCase
{
    private $pdoMock;
    private $pdoStatementMock;
    private $viewRepository;

    public function setUp()
    {
        $this->pdoMock = $this->createMock(\PDO::class);
        $this->pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->viewRepository = new FollowViewRepository($this->pdoMock);
    }

    public function testFollowThatDoesNotExistReturnsException()
    {
        $followId = FollowId::fromString('followId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `follow` WHERE follow_id = :follow_id LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute and fetch method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':follow_id' => (string) $followId]);
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn(false);

        $this->expectException(RepositoryException::class);
        $this->expectExceptionMessage('Follow not found.');
        $this->viewRepository->get($followId);
    }

    public function testItCanGetAFollow()
    {
        $followId = FollowId::fromString('followId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `follow` WHERE follow_id = :follow_id LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':follow_id' => (string) $followId]);
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn([
                'team_id' => 'blah',
                'follow_id' => 'blah',
                'group_id' => 'blah'
            ]);

        $this->viewRepository->get($followId);
    }

    public function testItCanGetAllFollowsOfATeam()
    {
        $teamId = TeamId::fromString('teamId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `follow` WHERE team_id = :team_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':team_id' => (string) $teamId]);

        // fetch method called
        $this->pdoStatementMock
            ->expects($this->atLeastOnce())
            ->method('fetch');

        $this->viewRepository->getAllByTeam($teamId);
    }
}
