<?php

namespace Tailgate\Tests\Infrastructure\Persistence\Projection\PDO;

use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Common\Email;
use Tailgate\Domain\Model\User\EmailUpdated;
use Tailgate\Domain\Model\User\PasswordResetTokenApplied;
use Tailgate\Domain\Model\User\PasswordUpdated;
use Tailgate\Domain\Model\User\UserActivated;
use Tailgate\Domain\Model\User\UserDeleted;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRegistered;
use Tailgate\Domain\Model\User\UserRole;
use Tailgate\Domain\Model\User\UserStatus;
use Tailgate\Domain\Model\User\UserUpdated;
use Tailgate\Infrastructure\Persistence\Projection\PDO\UserProjection;
use Tailgate\Test\BaseTestCase;

class PDOUserProjectionTest extends BaseTestCase
{
    private $pdoMock;
    private $pdoStatementMock;
    private $projection;

    public function setUp(): void
    {
        $this->pdoMock = $this->createMock(\PDO::class);
        $this->pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->projection = new UserProjection($this->pdoMock);
    }

    public function testItCanProjectUserRegistered()
    {
        $event = new UserRegistered(UserId::fromString('userId'), Email::fromString('email@email.com'), 'password1', UserStatus::fromString('Active'), UserRole::fromString('Admin'), Date::fromDateTimeImmutable($this->getFakeTime()->currentTime()));

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO `user` (user_id, password_hash, email, status, role, created_at) VALUES (:user_id, :password_hash, :email, :status, :role, :created_at)')
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
                ':created_at' => $event->getDateOccurred(),
            ]);

        $this->projection->projectUserRegistered($event);
    }

    public function testItCanProjectUserActivated()
    {
        $event = new UserActivated(UserId::fromString('userId'), UserStatus::fromString('Active'), Date::fromDateTimeImmutable($this->getFakeTime()->currentTime()));

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE `user` SET status = :status WHERE user_id = :user_id')
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
        $event = new UserDeleted(UserId::fromString('userId'), 'status', Date::fromDateTimeImmutable($this->getFakeTime()->currentTime()));

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE `user` SET status = :status WHERE user_id = :user_id')
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
        $event = new PasswordUpdated(UserId::fromString('userId'), 'password', Date::fromDateTimeImmutable($this->getFakeTime()->currentTime()));

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE `user` SET password_hash = :password_hash WHERE user_id = :user_id')
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
        $event = new EmailUpdated(UserId::fromString('userId'), Email::fromString('email@email.com'), Date::fromDateTimeImmutable($this->getFakeTime()->currentTime()));

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE `user` SET email = :email WHERE user_id = :user_id')
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
        $event = new UserUpdated(UserId::fromString('userId'), Email::fromString('email@email.com'), UserStatus::fromString('Active'), UserRole::fromString('Admin'), Date::fromDateTimeImmutable($this->getFakeTime()->currentTime()));

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE `user` SET email = :email, status = :status, role = :role WHERE user_id = :user_id')
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

    public function testItCanProjectPasswordResetTokenApplied()
    {
        $event = new PasswordResetTokenApplied(UserId::fromString('userId'), 'token', Date::fromDateTimeImmutable($this->getFakeTime()->currentTime()));

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE `user` SET password_reset_token = :password_reset_token WHERE user_id = :user_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':user_id' => $event->getAggregateId(),
                ':password_reset_token' => $event->getPasswordResetToken(),
            ]);

        $this->projection->projectPasswordResetTokenApplied($event);
    }
}
