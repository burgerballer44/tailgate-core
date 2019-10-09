<?php

namespace Tailgate\Tests\Infrastructure\Persistence\ViewRepository\PDO;

use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupView;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\GroupViewRepository;
use Tailgate\Infrastructure\Persistence\ViewRepository\RepositoryException;

class PDOGroupViewRepositoryTest extends TestCase
{
    private $pdoMock;
    private $pdoStatementMock;
    private $viewRepository;

    public function setUp()
    {
        $this->pdoMock = $this->createMock(\PDO::class);
        $this->pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->viewRepository = new GroupViewRepository($this->pdoMock);
    }

    public function testGroupThatDoesNotExistReturnsException()
    {
        $groupId = GroupId::fromString('groupId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `group` WHERE group_id = :group_id LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute and fetch method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':group_id' => (string) $groupId]);

        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn(false);

        $this->expectException(RepositoryException::class);
        $this->expectExceptionMessage('Group not found.');
        $this->viewRepository->get($groupId);
    }

    public function testItCanGetAGroup()
    {
        $groupId = GroupId::fromString('groupId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `group` WHERE group_id = :group_id LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':group_id' => (string) $groupId]);
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn([
                'group_id' => 'blah',
                'name' => 'blah',
                'owner_id' => 'blah',
            ]);

        $this->viewRepository->get($groupId);
    }

    public function testItCanGetAllGroupsForAUser()
    {
        $userId = UserId::fromString('userId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT `group`.group_id, `group`.name, `group`.owner_id
            FROM `group`
            JOIN `member` on `member`.group_id = `group`.group_id
            WHERE `member`.user_id = :user_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':user_id' => (string) $userId]);

        // fetch method called
        $this->pdoStatementMock
            ->expects($this->atLeastOnce())
            ->method('fetch');

        $this->viewRepository->all($userId);
    }

    public function testItCanQueryGroups()
    {
        $userId = UserId::fromString('userId');
        $name = 'name';

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with("SELECT * FROM `group`
            JOIN `member` on `member`.group_id = `group`.group_id WHERE 1=1  AND  user_id = :user_id  AND  name LIKE :name ")
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':user_id' => (string) $userId, ':name' => "%{$name}%"]);

        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch');

        $this->viewRepository->query($userId, $name);
    }
}
