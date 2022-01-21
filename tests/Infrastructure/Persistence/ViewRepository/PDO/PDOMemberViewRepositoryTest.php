<?php

namespace Tailgate\Tests\Infrastructure\Persistence\ViewRepository\PDO;

use Tailgate\Test\BaseTestCase;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\MemberViewRepository;
use RuntimeException;

class PDOMemberViewRepositoryTest extends BaseTestCase
{
    private $pdoMock;
    private $pdoStatementMock;
    private $viewRepository;

    public function setUp(): void
    {
        $this->pdoMock = $this->createMock(\PDO::class);
        $this->pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->viewRepository = new MemberViewRepository($this->pdoMock);
    }

    public function testMemberThatDoesNotExistReturnsException()
    {
        $memberId = MemberId::fromString('memberId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT `member`.member_id, `member`.group_id, `member`.user_id, `member`.role, `member`.allow_multiple, `user`.email
            FROM `member`
            JOIN `user` on `user`.user_id = `member`.user_id
            WHERE `member`.member_id = :member_id LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute and fetch method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':member_id' => (string) $memberId]);
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn(false);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Member not found.');
        $this->viewRepository->get($memberId);
    }

    public function testItCanGetAMember()
    {
        $memberId = MemberId::fromString('memberId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT `member`.member_id, `member`.group_id, `member`.user_id, `member`.role, `member`.allow_multiple, `user`.email
            FROM `member`
            JOIN `user` on `user`.user_id = `member`.user_id
            WHERE `member`.member_id = :member_id LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':member_id' => (string) $memberId]);
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn([
                'member_id' => 'blah',
                'group_id' => 'blah',
                'user_id' => 'blah',
                'role' => 'blah',
                'allow_multiple' => 'blah',
                'email' => 'blah',
            ]);

        $this->viewRepository->get($memberId);
    }

    public function testItCanGetAllMembersOfAGroup()
    {
        $groupId = GroupId::fromString('groupId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT `member`.member_id, `member`.group_id, `member`.user_id, `member`.role, `member`.allow_multiple, `user`.email
            FROM `member`
            JOIN `user` on `user`.user_id = `member`.user_id
            WHERE `member`.group_id = :group_id')
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

    public function testItCanGetAllMembersByUser()
    {
        $userId = UserId::fromString('userId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT `member`.member_id, `member`.group_id, `member`.user_id, `member`.role, `member`.allow_multiple, `user`.email
            FROM `member`
            JOIN `user` on `user`.user_id = `member`.user_id
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

        $this->viewRepository->getAllByUser($userId);
    }
}
