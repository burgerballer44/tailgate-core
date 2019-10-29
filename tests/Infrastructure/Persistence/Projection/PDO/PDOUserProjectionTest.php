<?php

namespace Tailgate\Tests\Infrastructure\Persistence\Projection\PDO;

use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRegistered;
use Tailgate\Domain\Model\User\UserActivated;
use Tailgate\Domain\Model\User\UserDeleted;
use Tailgate\Domain\Model\User\PasswordUpdated;
use Tailgate\Domain\Model\User\EmailUpdated;
use Tailgate\Domain\Model\User\UserUpdated;
use Tailgate\Infrastructure\Persistence\Projection\PDO\UserProjection;

class PDOUserProjectionTest extends TestCase
{
    private $pdoMock;
    private $pdoStatementMock;
    private $projection;

    public function setUp()
    {
        $this->pdoMock = $this->createMock(\PDO::class);
        $this->pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->projection = new UserProjection($this->pdoMock);
    }

    public function testItCanProjectUserRegistered()
    {
        $event = new UserRegistered(UserId::fromString('userId'), 'email1', 'password1', 'status', 'role', 'randomString');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO `user` (user_id, password_hash, email, status, role, password_reset_token, created_at)
            VALUES (:user_id, :password_hash, :email, :status, :role, :password_reset_token, :created_at)')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':user_id' => $event->getAggregateId(),
                ':email' => $event->getEmail(),
                ':password_hash' => $event->getPasswordHash(),
                ':status' => $event->getStatus(),
                ':role' => $event->getRole(),
                ':password_reset_token' => $event->getPasswordResetToken(),
                ':created_at' => $event->getOccurredOn()->format('Y-m-d H:i:s')
            ]);

        $this->projection->projectUserRegistered($event);
    }

    public function testItCanProjectUserActivated()
    {
        $event = new UserActivated(UserId::fromString('userId'), 'status');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE `user`
            SET status = :status
            WHERE user_id = :user_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':user_id' => $event->getAggregateId(),
                ':status' => $event->getStatus(),
            ]);

        $this->projection->projectUserActivated($event);
    }

    public function testItCanProjectUserDeleted()
    {
        $event = new UserDeleted(UserId::fromString('userId'), 'status');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE `user`
            SET status = :status
            WHERE user_id = :user_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':user_id' => $event->getAggregateId(),
                ':status' => $event->getStatus(),
            ]);

        $this->projection->projectUserDeleted($event);
    }

    public function testItCanProjectPasswordUpdated()
    {
        $event = new PasswordUpdated(UserId::fromString('userId'), 'password');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE `user`
            SET password_hash = :password_hash
            WHERE user_id = :user_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':user_id' => $event->getAggregateId(),
                ':password_hash' => $event->getPasswordHash(),
            ]);

        $this->projection->projectPasswordUpdated($event);
    }

    public function testItCanProjectEmailUpdated()
    {
        $event = new EmailUpdated(UserId::fromString('userId'), 'email@email.com');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE `user`
            SET email = :email
            WHERE user_id = :user_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':user_id' => $event->getAggregateId(),
                ':email' => $event->getEmail(),
            ]);

        $this->projection->projectEmailUpdated($event);
    }

    public function testItCanProjectUserUpdated()
    {
        $event = new UserUpdated(UserId::fromString('userId'), 'email@email.com', 'status', 'role');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE `user`
            SET email = :email, status = :status, role = :role
            WHERE user_id = :user_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':user_id' => $event->getAggregateId(),
                ':email' => $event->getEmail(),
                ':status' => $event->getStatus(),
                ':role' => $event->getRole(),
            ]);

        $this->projection->projectUserUpdated($event);
    }
}
