<?php

namespace Tailgate\Test\Domain\Model\User;

use Buttercup\Protects\AggregateHistory;
use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupCreated;
use Tailgate\Domain\Model\User\UserId;

class GroupTest extends TestCase
{
    private $id;
    private $name;
    private $ownerId;

    public function setUp()
    {
        $this->id = new GroupId('idToCheck');
        $this->name = 'groupName';
        $this->ownerId = new UserId('ownerId');
    }

    public function testGroupShouldBeTheSameAfterReconstitution()
    {
        $group = Group::create($this->id, $this->name, $this->ownerId);
        $events = $group->getRecordedEvents();
        $group->clearRecordedEvents();

        $reconstitutedUser = Group::reconstituteFrom(
            new AggregateHistory($this->id, (array) $events)
        );

        $this->assertEquals($group, $reconstitutedUser,
            'the reconstituted group does not match the original group');
    }

    public function testGroupCreatedEventOccursWhenGroupIsCreated()
    {
        $group = Group::create($this->id, $this->name, $this->ownerId);
        $events = $group->getRecordedEvents();
        $group->clearRecordedEvents();

        $this->assertCount(1, $events);
        $this->assertTrue($events[0] instanceof GroupCreated);
        $this->assertTrue($events[0]->getAggregateId()->equals($this->id));
        $this->assertEquals($this->name, $events[0]->getName());
        $this->assertEquals($this->ownerId, $events[0]->getOwnerId());

        $this->assertCount(0, $group->getRecordedEvents());
    }
}
