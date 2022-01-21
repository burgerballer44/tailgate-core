<?php

namespace Tailgate\Tests\Infrastructure\Persistence\Repository\Publisher;

use Burger\Aggregate\AggregateHistory;
use Burger\Aggregate\DomainEvent;
use Burger\Event\EventPublisher;
use Burger\Event\EventPublisherInterface;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupDomainEvent;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupInviteCode;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Infrastructure\Persistence\Event\EventStoreInterface;
use Tailgate\Infrastructure\Persistence\Event\Subscriber\Projection\GroupProjectorEventSubscriber;
use Tailgate\Infrastructure\Persistence\Projection\GroupProjectionInterface;
use Tailgate\Infrastructure\Persistence\Repository\Publisher\GroupRepository;
use Tailgate\Test\BaseTestCase;

class PublisherGroupRepositoryTest extends BaseTestCase
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
        $this->group = Group::create(GroupId::fromString('GroupId'), 'Groupname', GroupInviteCode::create(), UserId::fromString('userId'), Date::fromDateTimeImmutable($this->getFakeTime()->currentTime()));
    }

    public function testItCanGetAGroup()
    {
        $groupId = $this->group->getGroupId();
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
