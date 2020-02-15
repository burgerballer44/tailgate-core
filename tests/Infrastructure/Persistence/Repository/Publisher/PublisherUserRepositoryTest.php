<?php

namespace Tailgate\Tests\Infrastructure\Persistence\Repository\Publisher;

use Buttercup\Protects\AggregateHistory;
use Buttercup\Protects\DomainEvent;
use PHPUnit\Framework\TestCase;
use Burger\EventPublisher;
use Burger\EventPublisherInterface;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserDomainEvent;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Infrastructure\Persistence\Projection\UserProjectionInterface;
use Tailgate\Infrastructure\Persistence\Event\EventStoreInterface;
use Tailgate\Infrastructure\Persistence\Event\UserProjectorEventSubscriber;
use Tailgate\Infrastructure\Persistence\Repository\Publisher\UserRepository;

class PublisherUserRepositoryTest extends TestCase
{
    private $eventStore;
    private $eventPublisher;
    private $projection;
    private $user;

    public function setUp(): void
    {
        $this->eventStore = $this->createMock(EventStoreInterface::class);
        $this->projection = $this->createMock(UserProjectionInterface::class);
        $this->eventPublisher = EventPublisher::instance();
        $this->eventPublisher->subscribe(new UserProjectorEventSubscriber($this->projection));

        // create a user and activate it just so we have an extra event on it
        $this->user = User::create(UserId::fromString('userId'), 'email', 'passwordHash');
        $this->user->activate();
    }

    public function testItCanGetAUser()
    {
        $userId = UserId::fromString($this->user->getId());
        $aggregateHistory = new AggregateHistory($userId, (array)$this->user->getRecordedEvents());

        // the getAggregateHistoryFor method should be called once and will return the aggregateHistory
        $this->eventStore->expects($this->once())->method('getAggregateHistoryFor')->willReturn($aggregateHistory);

        $userRepository = new UserRepository($this->eventStore, $this->eventPublisher);

        $user = $userRepository->get($userId);
        
        $this->assertInstanceOf(User::class, $user);
    }

    public function testItCanAddEventsToTheEventPublisher()
    {
        // the projectOne method should be called twice since the user has 2 events
        $this->projection->expects($this->exactly(2))->method('projectOne')->with($this->isInstanceOf(UserDomainEvent::class));

        $userRepository = new UserRepository($this->eventStore, $this->eventPublisher);

        $userRepository->add($this->user);
    }

    public function testItReturnsANewUserIdentity()
    {
        $userRepository = new UserRepository($this->eventStore, $this->eventPublisher);

        $userId = $userRepository->nextIdentity();

        $this->assertInstanceOf(UserId::class, $userId);
    }
}
