<?php

namespace Tailgate\Tests\Infrastructure\Persistence\Repository\Publisher;

use Buttercup\Protects\AggregateHistory;
use Buttercup\Protects\DomainEvent;
use PHPUnit\Framework\TestCase;
use Burger\EventPublisher;
use Burger\EventPublisherInterface;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupDomainEvent;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Infrastructure\Persistence\Projection\GroupProjectionInterface;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Infrastructure\Persistence\Event\EventStoreInterface;
use Tailgate\Infrastructure\Persistence\Event\GroupProjectorEventSubscriber;
use Tailgate\Infrastructure\Persistence\Repository\Publisher\GroupRepository;

class PublisherGroupRepositoryTest extends TestCase
{
    private $eventStore;
    private $projection;
    private $eventPublisher;
    private $group;

    public function setUp(): void
    {
        $this->eventStore = $this->createMock(EventStoreInterface::class);
        $this->projection = $this->createMock(GroupProjectionInterface::class);
        $this->eventPublisher = EventPublisher::instance();
        $this->eventPublisher->subscribe(new GroupProjectorEventSubscriber($this->projection));

        // create a group so we have an event
        $this->group = Group::create(GroupId::fromString('GroupId'), 'Groupname', 'inviteCode', UserId::fromString('userId'));
    }

    public function testItCanGetAGroup()
    {
        $groupId = GroupId::fromString($this->group->getId());
        $aggregateHistory = new AggregateHistory($groupId, (array)$this->group->getRecordedEvents());

        // the getAggregateHistoryFor method should be called once and will return the aggregateHistory
        $this->eventStore->expects($this->once())->method('getAggregateHistoryFor')->willReturn($aggregateHistory);

        $groupRepository = new GroupRepository($this->eventStore, $this->eventPublisher);

        $group = $groupRepository->get($groupId);
        
        $this->assertInstanceOf(Group::class, $group);
    }

    public function testItCanAddEventsToTheEventPublisher()
    {
        // the publish method should be called twice since the Group has 2 events
        $this->projection->expects($this->exactly(2))->method('projectOne')->with($this->isInstanceOf(GroupDomainEvent::class));

        $groupRepository = new GroupRepository($this->eventStore, $this->eventPublisher);

        $groupRepository->add($this->group);
    }

    public function testItReturnsANewGroupIdentity()
    {
        $groupRepository = new GroupRepository($this->eventStore, $this->eventPublisher);

        $groupId = $groupRepository->nextIdentity();

        $this->assertInstanceOf(GroupId::class, $groupId);
    }
}
