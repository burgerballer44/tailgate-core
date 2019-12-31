<?php

namespace Tailgate\Tests\Infrastructure\Persistence\Repository\Publisher;

use Buttercup\Protects\AggregateHistory;
use Buttercup\Protects\DomainEvent;
use PHPUnit\Framework\TestCase;
use Tailgate\Common\Event\EventPublisher;
use Tailgate\Common\Event\EventPublisherInterface;
use Tailgate\Domain\Model\Team\Team;
use Tailgate\Domain\Model\Team\TeamDomainEvent;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamProjectionInterface;
use Tailgate\Infrastructure\Persistence\Event\EventStoreInterface;
use Tailgate\Infrastructure\Persistence\Event\TeamProjectorEventSubscriber;
use Tailgate\Infrastructure\Persistence\Repository\Publisher\TeamRepository;

class PublisherTeamRepositoryTest extends TestCase
{
    private $eventStore;
    private $projection;
    private $eventPublisher;
    private $team;

    public function setUp()
    {
        $this->eventStore = $this->createMock(EventStoreInterface::class);
        $this->projection = $this->createMock(TeamProjectionInterface::class);
        $this->eventPublisher = EventPublisher::instance();
        $this->eventPublisher->subscribe(new TeamProjectorEventSubscriber($this->projection));

        // create a team so we have an event
        $this->team = Team::create(teamId::fromString('teamId'), 'dedignation', 'mascot');
    }

    public function testItCanGetATeam()
    {
        $teamId = TeamId::fromString($this->team->getId());
        $aggregateHistory = new AggregateHistory($teamId, (array)$this->team->getRecordedEvents());

        // the getAggregateHistoryFor method should be called once and will return the aggregateHistory
        $this->eventStore->expects($this->once())->method('getAggregateHistoryFor')->willReturn($aggregateHistory);

        $teamRepository = new TeamRepository($this->eventStore, $this->eventPublisher);

        $team = $teamRepository->get($teamId);
        
        $this->assertInstanceOf(Team::class, $team);
    }

    public function testItCanAddEventsToTheEventPublisher()
    {
        // the projectOne method should be called once since the Team has 1 events
        $this->projection->expects($this->exactly(1))->method('projectOne')->with($this->isInstanceOf(TeamDomainEvent::class));

        $teamRepository = new TeamRepository($this->eventStore, $this->eventPublisher);

        $teamRepository->add($this->team);
    }

    public function testItReturnsANewTeamIdentity()
    {
        $teamRepository = new TeamRepository($this->eventStore, $this->eventPublisher);

        $teamId = $teamRepository->nextIdentity();

        $this->assertInstanceOf(TeamId::class, $teamId);
    }
}
