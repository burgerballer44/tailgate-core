<?php

namespace Tailgate\Tests\Infrastructure\Persistence\ViewRepository\PDO;

use RuntimeException;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\PlayerId;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\PlayerViewRepository;
use Tailgate\Test\BaseTestCase;

class PDOPlayerViewRepositoryTest extends BaseTestCase
{
    private $pdoMock;
    private $pdoStatementMock;
    private $viewRepository;

    public function setUp(): void
    {
        $this->pdoMock = $this->createMock(\PDO::class);
        $this->pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->viewRepository = new PlayerViewRepository($this->pdoMock);
    }

    public function testPlayerThatDoesNotExistReturnsException()
    {
        $playerId = PlayerId::fromString('playerId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `player` WHERE player_id = :player_id LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute and fetch method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':player_id' => (string) $playerId]);
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn(false);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Player not found.');
        $this->viewRepository->get($playerId);
    }

    public function testItCanGetAPlayer()
    {
        $playerId = PlayerId::fromString('playerId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `player` WHERE player_id = :player_id LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':player_id' => (string) $playerId]);
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn([
                'player_id' => 'blah',
                'member_id' => 'blah',
                'group_id' => 'blah',
                'username' => 'blah',
            ]);

        $this->viewRepository->get($playerId);
    }

    public function testItCanGetAllPlayersOfAGroup()
    {
        $groupId = GroupId::fromString('groupId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `player` WHERE group_id = :group_id')
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
}
