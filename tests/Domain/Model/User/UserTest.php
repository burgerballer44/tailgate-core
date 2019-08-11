<?php

namespace Tailgate\Test\Domain\Model\User;

use Buttercup\Protects\AggregateHistory;
use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRegistered;

class UserTest extends TestCase
{
    private $userId;
    private $username;
    private $passwordHash;
    private $email;
    private $uniqueKey;

    public function setUp()
    {
        $this->userId = UserId::fromString('userId');
        $this->username = 'username';
        $this->passwordHash = 'password';
        $this->email = 'email@email.com';
        $this->email = 'randomString';
    }

    public function testUserShouldBeTheSameAfterReconstitution()
    {
        $user = User::create(
            $this->userId, $this->username, $this->passwordHash, $this->email, $this->uniqueKey
        );
        $events = $user->getRecordedEvents();
        $user->clearRecordedEvents();

        $reconstitutedUser = User::reconstituteFrom(
            new AggregateHistory($this->userId, (array) $events)
        );

        $this->assertEquals($user, $reconstitutedUser,
            'the reconstituted user does not match the original user');
    }

    public function testAUserCanBeCreated()
    {
        $user = User::create($this->userId, $this->username, $this->passwordHash, $this->email, $this->uniqueKey);

        $this->assertEquals($this->userId, $user->getId());
        $this->assertEquals($this->username, $user->getUsername());
        $this->assertEquals($this->passwordHash, $user->getPasswordHash());
        $this->assertEquals($this->email, $user->getEmail());
        $this->assertEquals(User::STATUS_PENDING, $user->getStatus());
        $this->assertEquals(User::ROLE_USER, $user->getRole());
    }

    public function testAUserCanBeActivated()
    {
        $user = User::create($this->userId, $this->username, $this->passwordHash, $this->email, $this->uniqueKey);

        $user->activate();

        $this->assertEquals(User::STATUS_ACTIVE, $user->getStatus());
    }

    public function testAPasswordCanBeUpdated()
    {
        $newPassword = 'newPassword';
        $user = User::create($this->userId, $this->username, $this->passwordHash, $this->email, $this->uniqueKey);

        $user->updatePassword($newPassword);

        $this->assertEquals($newPassword, $user->getPasswordHash());
        $this->assertNotEquals($this->passwordHash, $user->getPasswordHash());
    }

    public function testAnEmailCanBeUpdated()
    {
        $newEmail = 'email@new.new';
        $user = User::create($this->userId, $this->username, $this->passwordHash, $this->email, $this->uniqueKey);

        $user->updateEmail($newEmail);

        $this->assertEquals($newEmail, $user->getEmail());
        $this->assertNotEquals($this->email, $user->getEmail());
    }
}
