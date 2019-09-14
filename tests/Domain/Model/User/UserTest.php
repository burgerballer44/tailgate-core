<?php

namespace Tailgate\Test\Domain\Model\User;

use Buttercup\Protects\AggregateHistory;
use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRegistered;
use Tailgate\Domain\Model\ModelException;

class UserTest extends TestCase
{
    private $userId;
    private $passwordHash = 'passwordHashBlahBlah';
    private $email = 'emailAddress';
    private $uniqueKey = '';

    public function setUp()
    {
        $this->userId = UserId::fromString('userId');
    }

    public function testUserShouldBeTheSameAfterReconstitution()
    {
        $user = User::create(
            $this->userId,
            $this->email,
            $this->passwordHash,
            $this->uniqueKey
        );
        $events = $user->getRecordedEvents();
        $user->clearRecordedEvents();

        $reconstitutedUser = User::reconstituteFrom(
            new AggregateHistory($this->userId, (array) $events)
        );

        $this->assertEquals(
            $user,
            $reconstitutedUser,
            'the reconstituted user does not match the original user'
        );
    }

    public function testAUserCanBeCreated()
    {
        $user = User::create($this->userId, $this->email, $this->passwordHash, $this->uniqueKey);

        $this->assertEquals($this->userId, $user->getId());
        $this->assertEquals($this->passwordHash, $user->getPasswordHash());
        $this->assertEquals($this->email, $user->getEmail());
        $this->assertEquals(User::STATUS_PENDING, $user->getStatus());
        $this->assertEquals(User::ROLE_USER, $user->getRole());
    }

    public function testAUserCanBeActivated()
    {
        $user = User::create($this->userId, $this->email, $this->passwordHash, $this->uniqueKey);

        $user->activate();

        $this->assertEquals(User::STATUS_ACTIVE, $user->getStatus());
    }

    public function testAPasswordCanBeUpdated()
    {
        $newPassword = 'newPassword';
        $user = User::create($this->userId, $this->email, $this->passwordHash, $this->uniqueKey);

        $user->updatePassword($newPassword);

        $this->assertEquals($newPassword, $user->getPasswordHash());
        $this->assertNotEquals($this->passwordHash, $user->getPasswordHash());
    }

    public function testAnEmailCanBeUpdated()
    {
        $newEmail = 'email@new.new';
        $user = User::create($this->userId, $this->email, $this->passwordHash, $this->uniqueKey);

        $user->updateEmail($newEmail);

        $this->assertEquals($newEmail, $user->getEmail());
        $this->assertNotEquals($this->email, $user->getEmail());
    }

    public function testAUserCanBeUpdated()
    {
        $email = 'email@email.com';
        $status = User::STATUS_PENDING;
        $role = User::ROLE_ADMIN;
        $user = User::create($this->userId, $this->email, $this->passwordHash, $this->uniqueKey);

        $user->update($email, $status, $role);

        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($status, $user->getStatus());
        $this->assertEquals($role, $user->getRole());
    }

    public function testUpdatingAUserThrowsExceptionsWithInvalidValues()
    {
        $user = User::create($this->userId, $this->email, $this->passwordHash, $this->uniqueKey);

        $this->expectException(ModelException::class);
        $this->expectExceptionMessage('Invalid role. Role does not exist.');
        $user->update('email@email.com', User::STATUS_PENDING, 'invalideRole');

        $this->expectException(ModelException::class);
        $this->expectExceptionMessage('Invalid status. Status does not exist.');
        $user->update('email@email.com', 'invalidStatus', User::ROLE_ADMIN);
    }
}
