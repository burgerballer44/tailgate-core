<?php

namespace Tailgate\Tests\Infrastructure\Persistence\Repository\Publisher;

use Buttercup\Protects\DomainEvent;
use Buttercup\Protects\AggregateHistory;
use PHPUnit\Framework\TestCase;
use Tailgate\Common\Event\EventStoreInterface;
use Tailgate\Common\Event\EventPublisherInterface;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Infrastructure\Persistence\Repository\Publisher\GroupRepository;

class PublisherGroupRepositoryTest extends TestCase
{
    private $eventStore;
    private $domainEventPublisher;
    private $group;

    public function setUp()
    {
        $this->eventStore = $this->createMock(EventStoreInterface::class);
        $this->domainEventPublisher = $this->createMock(EventPublisherInterface::class);

        // create a group so we have an event
        $this->group = Group::create(GroupId::fromString('GroupId'), 'Groupname', UserId::fromString('userId'));
    }

    public function testItCanGetAGroup()
    {
        $groupId = GroupId::fromString($this->group->getId());
        $aggregateHistory = new AggregateHistory($groupId, (array)$this->group->getRecordedEvents());

        // the getAggregateHistoryFor method should be called once and will return the aggregateHistory
        $this->eventStore->expects($this->once())->method('getAggregateHistoryFor')->willReturn($aggregateHistory);

        $groupRepository = new GroupRepository($this->eventStore, $this->domainEventPublisher);

        $group = $groupRepository->get($groupId);
        
        $this->assertInstanceOf(Group::class, $group);
    }

    public function testItCanAddEventsToTheDomainEventPublisher()
    {
        // the publish method should be called twice since the Group has 2 events
        $this->domainEventPublisher->expects($this->exactly(2))->method('publish')->with($this->isInstanceOf(DomainEvent::class));

        $groupRepository = new GroupRepository($this->eventStore, $this->domainEventPublisher);

        $groupRepository->add($this->group);
    }

    public function testItReturnsANewGroupIdentity()
    {
        $groupRepository = new GroupRepository($this->eventStore, $this->domainEventPublisher);

        $groupId = $groupRepository->nextIdentity();

        $this->assertInstanceOf(GroupId::class, $groupId);
    }
}
