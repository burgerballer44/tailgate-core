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
            ->with('SELECT `group`.group_id, `group`.name, `group`.invite_code, `group`.owner_id
            FROM `group`
            WHERE `group`.group_id = :group_id
            LIMIT 1')
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
            ->with('SELECT `group`.group_id, `group`.name, `group`.invite_code, `group`.owner_id
            FROM `group`
            WHERE `group`.group_id = :group_id
            LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute and fetch method called once
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
                'invite_code' => 'blah',
                'owner_id' => 'blah',
            ]);

        $this->viewRepository->get($groupId);
    }

    public function testGroupByUserThatDoesNotExistReturnsException()
    {
        $userId = UserId::fromString('userId');
        $groupId = GroupId::fromString('groupId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT `group`.group_id, `group`.name, `group`.invite_code, `group`.owner_id
            FROM `group`
            JOIN `member` on `member`.group_id = `group`.group_id
            WHERE `member`.user_id = :user_id
            AND `group`.group_id = :group_id
            LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute and fetch method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':user_id' => (string) $userId, ':group_id' => (string) $groupId]);

        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn(false);

        $this->expectException(RepositoryException::class);
        $this->expectExceptionMessage('Group not found.');
        $this->viewRepository->getByUser($userId, $groupId);
    }

    public function testItCanGetAGroupByUser()
    {
        $userId = UserId::fromString('userId');
        $groupId = GroupId::fromString('groupId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT `group`.group_id, `group`.name, `group`.invite_code, `group`.owner_id
            FROM `group`
            JOIN `member` on `member`.group_id = `group`.group_id
            WHERE `member`.user_id = :user_id
            AND `group`.group_id = :group_id
            LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute and fetch method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':user_id' => (string) $userId, ':group_id' => (string) $groupId]);
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn([
                'group_id' => 'blah',
                'name' => 'blah',
                'invite_code' => 'blah',
                'owner_id' => 'blah',
            ]);

        $this->viewRepository->getByUser($userId, $groupId);
    }

    public function testItCanGetAllGroups()
    {
        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT `group`.group_id, `group`.name, `group`.invite_code, `group`.owner_id
            FROM `group`')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute');

        // fetch method called
        $this->pdoStatementMock
            ->expects($this->atLeastOnce())
            ->method('fetch');

        $this->viewRepository->all();
    }

    public function testItCanGetAllGroupsForAUser()
    {
        $userId = UserId::fromString('userId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT `group`.group_id, `group`.name, `group`.invite_code, `group`.owner_id
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

        $this->viewRepository->allByUser($userId);
    }

    public function testItCanQueryGroups()
    {
        $userId = UserId::fromString('userId');
        $name = 'name';

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with("SELECT DISTINCT `group`.group_id, `group`.name, `group`.invite_code, `group`.owner_id FROM `group`
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

    public function testGroupThatDoesNotExistByInviteCodeReturnsException()
    {
        $inviteCode = 'code';

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT `group`.group_id, `group`.name, `group`.invite_code, `group`.owner_id
            FROM `group`
            WHERE `group`.invite_code = :invite_code
            LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute and fetch method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':invite_code' => $inviteCode]);

        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn(false);

        $this->expectException(RepositoryException::class);
        $this->expectExceptionMessage('Group not found by invite code.');
        $this->viewRepository->byInviteCode($inviteCode);
    }

    public function testCanGetAGroupByInviteCode()
    {
        $inviteCode = 'code';

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT `group`.group_id, `group`.name, `group`.invite_code, `group`.owner_id
            FROM `group`
            WHERE `group`.invite_code = :invite_code
            LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute and fetch method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':invite_code' => $inviteCode]);

        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn([
                'group_id' => 'blah',
                'name' => 'blah',
                'invite_code' => 'blah',
                'owner_id' => 'blah',
            ]);

        $this->viewRepository->byInviteCode($inviteCode);
    }
}
