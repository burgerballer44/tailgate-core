<?php

namespace Tailgate\Tests\Infrastructure\Persistence\Projection\PDO;

use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\Team\TeamAdded;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamFollowed;
use Tailgate\Infrastructure\Persistence\Projection\PDO\PDOTeamProjection;

class PDOTeamProjectionTest extends TestCase
{
    private $pdoMock;
    private $pdoStatementMock;
    private $projection;

    public function setUp()
    {
        $this->pdoMock = $this->createMock(\PDO::class);
        $this->pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->projection = new PDOTeamProjection($this->pdoMock);
    }

    public function testItCanProjectTeamAdded()
    {
        $event = new TeamAdded(UserId::fromString('idToCheck1'), 'username1', 'password1', 'email1', 'status', 'role');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO user (user_id, username, password_hash, email, status, role, created_at)
            VALUES (:user_id, :username, :password_hash, :email, :status, :role, :created_at)')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':user_id' => $event->getAggregateId(),
                ':username' => $event->getUsername(),
                ':password_hash' => $event->getPasswordHash(),
                ':email' => $event->getEmail(),
                ':status' => $event->getStatus(),
                ':role' => $event->getRole(),
                ':created_at' => $event->getOccurredOn()->format('Y-m-d H:i:s')
            ]);

        $this->projection->projectUserSignedUp($event);
    }

    public function testItCanProjectTeamFollowed()
    {
        $event = new TeamFollowed(UserId::fromString('idToCheck1'), 'username1', 'password1', 'email1', 'status', 'role');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO user (user_id, username, password_hash, email, status, role, created_at)
            VALUES (:user_id, :username, :password_hash, :email, :status, :role, :created_at)')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':user_id' => $event->getAggregateId(),
                ':username' => $event->getUsername(),
                ':password_hash' => $event->getPasswordHash(),
                ':email' => $event->getEmail(),
                ':status' => $event->getStatus(),
                ':role' => $event->getRole(),
                ':created_at' => $event->getOccurredOn()->format('Y-m-d H:i:s')
            ]);

        $this->projection->projectUserSignedUp($event);
    }
}
