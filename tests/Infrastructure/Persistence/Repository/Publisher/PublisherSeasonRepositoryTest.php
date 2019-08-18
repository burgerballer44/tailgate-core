<?php

namespace Tailgate\Tests\Infrastructure\Persistence\Repository\Publisher;

use Buttercup\Protects\DomainEvent;
use Buttercup\Protects\AggregateHistory;
use PHPUnit\Framework\TestCase;
use Tailgate\Common\Event\EventStoreInterface;
use Tailgate\Common\Event\EventPublisherInterface;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Infrastructure\Persistence\Repository\Publisher\SeasonRepository;

class PublisherSeasonRepositoryTest extends TestCase
{
    private $eventStore;
    private $domainEventPublisher;
    private $season;

    public function setUp()
    {
        $this->eventStore = $this->createMock(EventStoreInterface::class);
        $this->domainEventPublisher = $this->createMock(EventPublisherInterface::class);

        // create a season so we have an event
        $this->season = Season::create(
            SeasonId::fromString('SeasonId'),
            'name',
            'sport',
            'type',
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );
    }

    public function testItCanGetASeason()
    {
        $seasonId = SeasonId::fromString($this->season->getId());
        $aggregateHistory = new AggregateHistory($seasonId, (array)$this->season->getRecordedEvents());

        // the getAggregateHistoryFor method should be called once and will return the aggregateHistory
        $this->eventStore->expects($this->once())->method('getAggregateHistoryFor')->willReturn($aggregateHistory);

        $seasonRepository = new SeasonRepository($this->eventStore, $this->domainEventPublisher);

        $season = $seasonRepository->get($seasonId);
        
        $this->assertInstanceOf(Season::class, $season);
    }

    public function testItCanAddEventsToTheDomainEventPublisher()
    {
        // the publish method should be called twice since the Season has 1 events
        $this->domainEventPublisher->expects($this->exactly(1))->method('publish')->with($this->isInstanceOf(DomainEvent::class));

        $seasonRepository = new SeasonRepository($this->eventStore, $this->domainEventPublisher);

        $seasonRepository->add($this->season);
    }

    public function testItReturnsANewSeasonIdentity()
    {
        $seasonRepository = new SeasonRepository($this->eventStore, $this->domainEventPublisher);

        $seasonId = $seasonRepository->nextIdentity();

        $this->assertInstanceOf(SeasonId::class, $seasonId);
    }
}
