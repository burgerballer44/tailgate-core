<?php

namespace Tailgate\Tests\Infrastructure\Persistence\ViewRepository\PDO;

use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserView;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\UserViewRepository;
use Tailgate\Infrastructure\Persistence\ViewRepository\RepositoryException;

class PDOUserViewRepositoryTest extends TestCase
{
    private $pdoMock;
    private $pdoStatementMock;
    private $viewRepository;

    public function setUp(): void
    {
        $this->pdoMock = $this->createMock(\PDO::class);
        $this->pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->viewRepository = new UserViewRepository($this->pdoMock);
    }

    public function testUserThatDoesNotExistReturnsException()
    {
        $userId = UserId::fromString('userId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `user` WHERE user_id = :user_id LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute and fetch method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':user_id' => (string) $userId]);
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn(false);

        $this->expectException(RepositoryException::class);
        $this->expectExceptionMessage('User not found.');
        $this->viewRepository->get($userId);
    }

    public function testItCanGetAUser()
    {
        $userId = UserId::fromString('userId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `user` WHERE user_id = :user_id LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute and fetch method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':user_id' => (string) $userId]);
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn([
                'user_id' => 'blah',
                'email' => 'blah',
                'status' => 'blah',
                'role' => 'blah',
            ]);

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

    public function testItCanGetAUserByEmail()
    {
        $email = 'email@email.com';

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `user` WHERE email = :email LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute and fetch method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':email' => (string) $email]);
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn([
                'user_id' => 'blah',
                'email' => 'blah',
                'status' => 'blah',
                'role' => 'blah',
            ]);

        $this->viewRepository->byEmail($email);
    }

    public function testEmailThatDoesNotExistThrowsException()
    {
        $email = 'email@email.com';

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `user` WHERE email = :email LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute and fetch method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':email' => (string) $email]);
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn(false);

        $this->expectException(RepositoryException::class);
        $this->expectExceptionMessage('User not found by email.');
        $this->viewRepository->byEmail($email);
    }

    public function testItCanGetAUserByPasswordResetToken()
    {
        $passwordResetToken = 'slsjdhjkhsdjkdh_' . time();

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `user` WHERE password_reset_token = :password_reset_token LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute and fetch method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':password_reset_token' => (string) $passwordResetToken]);
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn([
                'user_id' => 'blah',
                'email' => 'blah',
                'status' => 'blah',
                'role' => 'blah',
            ]);

        $this->viewRepository->byPasswordResetToken($passwordResetToken);
    }

    public function testPasswordResetTokenThatDoesNotExistReturnsException()
    {
        $passwordResetToken = 'assa;ldkjsadfj;l';

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `user` WHERE password_reset_token = :password_reset_token LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute and fetch method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':password_reset_token' => (string) $passwordResetToken]);
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn(false);

        $this->expectException(RepositoryException::class);
        $this->expectExceptionMessage('User not found by reset token.');
        $this->viewRepository->byPasswordResetToken($passwordResetToken);
    }

    public function testPasswordResetTokenThatExpiredReturnsException()
    {
        $expiredPasswordResetToken = 'asaldkjsadfjl_0000000000';

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `user` WHERE password_reset_token = :password_reset_token LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute and fetch method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':password_reset_token' => (string) $expiredPasswordResetToken]);
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn([
                'user_id' => 'blah',
                'email' => 'blah',
                'status' => 'blah',
                'role' => 'blah',
            ]);

        $this->expectException(RepositoryException::class);
        $this->expectExceptionMessage('Reset token expired. Please request a password reset again.');
        $this->viewRepository->byPasswordResetToken($expiredPasswordResetToken);
    }
}
