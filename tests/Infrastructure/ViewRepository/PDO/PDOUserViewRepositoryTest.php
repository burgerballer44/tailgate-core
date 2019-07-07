<?php

namespace Tailgate\Tests\Infrastructure\Persistence\ViewRepository\PDO;

use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserView;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\UserViewRepository;

class PDOUserViewRepositoryTest extends TestCase
{
    private $pdoMock;
    private $pdoStatementMock;
    private $viewRepository;

    public function setUp()
    {
        $this->pdoMock = $this->createMock(\PDO::class);
        $this->pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->viewRepository = new UserViewRepository($this->pdoMock);
    }

    public function testItCanGetAUser()
    {
        $userId = UserId::fromString('userId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `user` WHERE user_id = :user_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':user_id' => (string) $userId]);

        $this->viewRepository->get($userId);
    }

    public function testItCanGetAllUsers()
    {
        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `user`')
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
}
