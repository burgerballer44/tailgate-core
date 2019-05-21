<?php

namespace Tailgate\Test\Domain\Model\User;

use Buttercup\Protects\AggregateHistory;
use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserSignedUp;

class UserTest extends TestCase
{
    public function testUserIsSignedUpEventOccursOnCreate()
    {
        $id = new UserId('idToCheck');
        $username = 'username';
        $password = 'password';
        $email = 'email@email.com';

        $user = User::create($id, $username, $password, $email);
        $events = $user->getRecordedEvents();

        $this->assertCount(1, $events);
        $this->assertTrue($events[0] instanceof UserSignedUp);
        $this->assertTrue($events[0]->getAggregateId()->equals($id));
        $this->assertEquals($username, $events[0]->getUsername());
        $this->assertEquals($password, $events[0]->getPassword());
        $this->assertEquals($email, $events[0]->getEmail());

        $user->clearRecordedEvents();
        $this->assertCount(0, $user->getRecordedEvents());
    }

    public function testItShouldBeTheSameAfterReconstitution()
    {
        $id = new UserId('idToCheck');
        $username = 'username';
        $password = 'password';
        $email = 'email@email.com';

        $user = User::create($id, $username, $password, $email);

        $events = $user->getRecordedEvents();
        $user->clearRecordedEvents();

        $reconstitutedUser = User::reconstituteFrom(
            new AggregateHistory($id, (array) $events)
        );

        $this->assertEquals($user, $reconstitutedUser);
    }
}
