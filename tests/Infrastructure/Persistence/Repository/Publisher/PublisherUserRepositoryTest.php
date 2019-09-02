<?php

namespace Tailgate\Tests\Infrastructure\Persistence\Repository\Publisher;

use Buttercup\Protects\DomainEvent;
use Buttercup\Protects\AggregateHistory;
use PHPUnit\Framework\TestCase;
use Tailgate\Common\Event\EventStoreInterface;
use Tailgate\Common\Event\EventPublisherInterface;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Infrastructure\Persistence\Repository\Publisher\UserRepository;

class PublisherUserRepositoryTest extends TestCase
{
    private $eventStore;
    private $domainEventPublisher;
    private $user;

    public function setUp()
    {
        $this->eventStore = $this->createMock(EventStoreInterface::class);
        $this->domainEventPublisher = $this->createMock(EventPublisherInterface::class);

        // create a user and activate it just so we have an extra event on it
        $this->user = User::create(UserId::fromString('userId'), 'email', 'passwordHash', 'uniqueKey');
        $this->user->activate();
    }

    public function testItCanGetAUser()
    {
        $userId = UserId::fromString($this->user->getId());
        $aggregateHistory = new AggregateHistory($userId, (array)$this->user->getRecordedEvents());

        // the getAggregateHistoryFor method should be called once and will return the aggregateHistory
        $this->eventStore->expects($this->once())->method('getAggregateHistoryFor')->willReturn($aggregateHistory);

        $userRepository = new UserRepository($this->eventStore, $this->domainEventPublisher);

        $user = $userRepository->get($userId);
        
        $this->assertInstanceOf(User::class, $user);
    }

    public function testItCanAddEventsToTheDomainEventPublisher()
    {
        // the publish method should be called twice since the user has 2 events
        $this->domainEventPublisher->expects($this->exactly(2))->method('publish')->with($this->isInstanceOf(DomainEvent::class));

        $userRepository = new UserRepository($this->eventStore, $this->domainEventPublisher);

        $userRepository->add($this->user);
    }

    public function testItReturnsANewUserIdentity()
    {
        $userRepository = new UserRepository($this->eventStore, $this->domainEventPublisher);

        $userId = $userRepository->nextIdentity();

        $this->assertInstanceOf(UserId::class, $userId);
    }
}
