<?php

namespace Tailgate\Tests\Infrastructure\Persistence\ViewRepository\PDO;

use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\MemberViewRepository;

class PDOMemberViewRepositoryTest extends TestCase
{
    private $pdoMock;
    private $pdoStatementMock;
    private $viewRepository;

    public function setUp()
    {
        $this->pdoMock = $this->createMock(\PDO::class);
        $this->pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->viewRepository = new MemberViewRepository($this->pdoMock);
    }

    public function testItCanGetAMember()
    {
        $memberId = MemberId::fromString('memberId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `member` WHERE member_id = :member_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':member_id' => (string) $memberId]);

        $this->viewRepository->get($memberId);
    }

    public function testItCanGetAllMembersOfAGroup()
    {
        $groupId = GroupId::fromString('groupId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `member` WHERE group_id = :group_id')
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
