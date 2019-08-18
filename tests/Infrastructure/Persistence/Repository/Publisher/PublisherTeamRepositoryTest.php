<?php

namespace Tailgate\Tests\Infrastructure\Persistence\Repository\Publisher;

use Buttercup\Protects\DomainEvent;
use Buttercup\Protects\AggregateHistory;
use PHPUnit\Framework\TestCase;
use Tailgate\Common\Event\EventStoreInterface;
use Tailgate\Common\Event\EventPublisherInterface;
use Tailgate\Domain\Model\Team\Team;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Infrastructure\Persistence\Repository\Publisher\TeamRepository;

class PublisherTeamRepositoryTest extends TestCase
{
    private $eventStore;
    private $domainEventPublisher;
    private $team;

    public function setUp()
    {
        $this->eventStore = $this->createMock(EventStoreInterface::class);
        $this->domainEventPublisher = $this->createMock(EventPublisherInterface::class);

        // create a team so we have an event 
        $this->team = Team::create(teamId::fromString('teamId'),'dedignation','mascot');
    }

    public function testItCanGetATeam()
    {   
        $teamId = TeamId::fromString($this->team->getId());
        $aggregateHistory = new AggregateHistory($teamId, (array)$this->team->getRecordedEvents());

        // the getAggregateHistoryFor method should be called once and will return the aggregateHistory
        $this->eventStore->expects($this->once())->method('getAggregateHistoryFor')->willReturn($aggregateHistory);

        $teamRepository = new TeamRepository($this->eventStore, $this->domainEventPublisher);

        $team = $teamRepository->get($teamId);
        
        $this->assertInstanceOf(Team::class, $team);
    }

    public function testItCanAddEventsToTheDomainEventPublisher()
    {
        // the publish method should be called twice since the Team has 1 events 
        $this->domainEventPublisher->expects($this->exactly(1))->method('publish')->with($this->isInstanceOf(DomainEvent::class));

        $teamRepository = new TeamRepository($this->eventStore, $this->domainEventPublisher);

        $teamRepository->add($this->team);
    }

    public function testItReturnsANewTeamIdentity()
    {
        $teamRepository = new TeamRepository($this->eventStore, $this->domainEventPublisher);

        $teamId = $teamRepository->nextIdentity();

        $this->assertInstanceOf(TeamId::class, $teamId);
    }
}
