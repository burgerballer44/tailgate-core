<?php

namespace Tailgate\Tests\Infrastructure\Persistence\Repository\Publisher;

use Burger\Aggregate\AggregateHistory;
use Burger\Aggregate\DomainEvent;
use Burger\Event\EventPublisher;
use Burger\Event\EventPublisherInterface;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Common\DateOrString;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\SeasonDomainEvent;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonType;
use Tailgate\Domain\Model\Season\Sport;
use Tailgate\Infrastructure\Persistence\Event\EventStoreInterface;
use Tailgate\Infrastructure\Persistence\Event\Subscriber\Projection\SeasonProjectorEventSubscriber;
use Tailgate\Infrastructure\Persistence\Projection\SeasonProjectionInterface;
use Tailgate\Infrastructure\Persistence\Repository\Publisher\SeasonRepository;
use Tailgate\Test\BaseTestCase;

class PublisherSeasonRepositoryTest extends BaseTestCase
{
    private $eventStore;
    private $projection;
    private $eventPublisher;
    private $season;

    public function setUp(): void
    {
        $this->eventStore = $this->createMock(EventStoreInterface::class);
        $this->projection = $this->createMock(SeasonProjectionInterface::class);
        $this->eventPublisher = EventPublisher::instance();
        $this->eventPublisher->subscribe(new SeasonProjectorEventSubscriber($this->projection));

        // create a season so we have an event
        $this->season = Season::create(
            SeasonId::fromString('SeasonId'),
            'name',
            Sport::getFootball(),
            SeasonType::getRegularSeason(),
            DateOrString::fromString('2019-05-05'),
            DateOrString::fromString('2019-05-15'),
            Date::fromDateTimeImmutable($this->getFakeTime()->currentTime())
        );
    }

    public function testItCanGetASeason()
    {
        $seasonId = $this->season->getSeasonId();
        $aggregateHistory = new AggregateHistory($seasonId, (array)$this->season->getRecordedEvents());

        // the getAggregateHistoryFor method should be called once and will return the aggregateHistory
        $this->eventStore->expects($this->once())->method('getAggregateHistoryFor')->willReturn($aggregateHistory);

        $seasonRepository = new SeasonRepository($this->eventStore, $this->eventPublisher);

        $season = $seasonRepository->get($seasonId);
        
        $this->assertInstanceOf(Season::class, $season);
    }

    public function testItCanAddEventsToTheEventPublisher()
    {
        // the projectOne method should be called once since the Season has 1 events
        $this->projection->expects($this->exactly(1))->method('projectOne')->with($this->isInstanceOf(SeasonDomainEvent::class));

        $seasonRepository = new SeasonRepository($this->eventStore, $this->eventPublisher);

        $seasonRepository->add($this->season);
    }

    public function testItReturnsANewSeasonIdentity()
    {
        $seasonRepository = new SeasonRepository($this->eventStore, $this->eventPublisher);

        $seasonId = $seasonRepository->nextIdentity();

        $this->assertInstanceOf(SeasonId::class, $seasonId);
    }
}
