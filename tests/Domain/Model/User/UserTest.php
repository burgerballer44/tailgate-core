<?php

namespace Tailgate\Test\Domain\Model\User;

use Buttercup\Protects\AggregateHistory;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserSignedUp;
use Tailgate\Tests\BaseTestCase;

class UserTest extends BaseTestCase
{
    private $userId;
    private $username;
    private $passwordHash;
    private $email;

    public function setUp()
    {
        $this->userId = UserId::fromString('userId');
        $this->username = 'username';
        $this->passwordHash = 'password';
        $this->email = 'email@email.com';
    }

    public function testUserShouldBeTheSameAfterReconstitution()
    {
        $user = User::create(
            $this->userId, $this->username, $this->passwordHash, $this->email
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
        $user = User::create($this->userId, $this->username, $this->passwordHash, $this->email);

        $this->assertEquals($this->userId, $user->getId());
        $this->assertEquals($this->username, $user->getUsername());
        $this->assertEquals($this->passwordHash, $user->getPasswordHash());
        $this->assertEquals($this->email, $user->getEmail());
        $this->assertEquals(User::STATUS_PENDING, $user->getStatus());
        $this->assertEquals(User::ROLE_USER, $user->getRole());
    }
}
