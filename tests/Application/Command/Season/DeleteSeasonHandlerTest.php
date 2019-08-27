<?php

namespace Tailgate\Test\Application\Command\Season;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Season\DeleteSeasonCommand;
use Tailgate\Application\Command\Season\DeleteSeasonHandler;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonDeleted;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;

class DeleteSeasonHandlerTest extends TestCase
{
    private $seasonId = 'seasonId';
    private $sport = 'sport';
    private $seasonType = 'seasonType';
    private $name = 'name';
    private $seasonStart;
    private $seasonEnd;
    private $season;
    private $deleteSeasonCommand;

    public function setUp()
    {
        // create season
        $this->seasonStart = \DateTimeImmutable::createFromFormat('Y-m-d', '2021-09-01');
        $this->seasonEnd = \DateTimeImmutable::createFromFormat('Y-m-d', '2021-12-28');
        $this->season = Season::create(
            SeasonId::fromString($this->seasonId),
            $this->sport,
            $this->seasonType,
            $this->name,
            $this->seasonStart,
            $this->seasonEnd
        );
        $this->season->clearRecordedEvents();

        $this->deleteSeasonCommand = new DeleteSeasonCommand(
            SeasonId::fromString($this->seasonId)
        );
    }

    public function testItAddsASeasonDeletedEventToTheSeasonRepository()
    {
        $seasonId = $this->seasonId;
        $season = $this->season;

        $seasonRepository = $this->getMockBuilder(SeasonRepositoryInterface::class)->getMock();

        // the nextIdentity method should be called once and will return a the season
        $seasonRepository
           ->expects($this->once())
           ->method('get')
           ->willReturn($season);

        // the add method should be called once
        // the season object should have the SeasonDeleted event
        $seasonRepository
            ->expects($this->once())
            ->method('add')
            ->with($this->callback(
                function ($season) use ($seasonId) {
                    $events = $season->getRecordedEvents();

                    return $events[0] instanceof SeasonDeleted
                && $events[0]->getAggregateId()->equals(SeasonId::fromString($seasonId))
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
                }
        ));

        $deleteSeasonHandler = new DeleteSeasonHandler(
            $seasonRepository
        );

        $deleteSeasonHandler->handle($this->deleteSeasonCommand);
    }
}
