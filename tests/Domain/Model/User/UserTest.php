<?php

namespace Tailgate\Test\Domain\Model\User;

use Buttercup\Protects\AggregateHistory;
use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserSignedUp;

class UserTest extends TestCase
{
    private $id;
    private $username;
    private $password;
    private $email;

    public function setUp()
    {
        $this->id = new UserId('idToCheck');
        $this->username = 'username';
        $this->password = 'password';
        $this->email = 'email@email.com';
    }

    public function testUserShouldBeTheSameAfterReconstitution()
    {
        $user = User::create($this->id, $this->username, $this->password, $this->email);
        $events = $user->getRecordedEvents();
        $user->clearRecordedEvents();

        $reconstitutedUser = User::reconstituteFrom(
            new AggregateHistory($this->id, (array) $events)
        );

        $this->assertEquals($user, $reconstitutedUser,
            'the reconstituted user does not match the original user');
    }

    public function testUserSignedUpEventOccursWhenUserIsCreated()
    {
        $user = User::create($this->id, $this->username, $this->password, $this->email);
        $events = $user->getRecordedEvents();
        $user->clearRecordedEvents();

        $this->assertCount(1, $events);
        $this->assertTrue($events[0] instanceof UserSignedUp);
        $this->assertTrue($events[0]->getAggregateId()->equals($this->id));
        $this->assertEquals($this->username, $events[0]->getUsername());
        $this->assertEquals($this->password, $events[0]->getPassword());
        $this->assertEquals($this->email, $events[0]->getEmail());
        $this->assertTrue($events[0]->getOccuredOn() instanceof \DateTimeImmutable);

        $this->assertCount(0, $user->getRecordedEvents());
    }
}
