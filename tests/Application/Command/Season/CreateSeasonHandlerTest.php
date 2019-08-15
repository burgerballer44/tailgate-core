<?php

namespace Tailgate\Test\Application\Command\Season;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Season\CreateSeasonCommand;
use Tailgate\Application\Command\Season\CreateSeasonHandler;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonCreated;
use Tailgate\Infrastructure\Persistence\Repository\SeasonRepository;

class CreateSeasonHandlerTest extends TestCase
{
    private $sport = 'sport';
    private $seasonType = 'seasonType';
    private $name = 'name';
    private $seasonStart;
    private $seasonEnd;
    private $createSeasonCommand;

    public function setUp()
    {
        $this->seasonStart = \DateTimeImmutable::createFromFormat('Y-m-d', '2019-09-01');
        $this->seasonEnd = \DateTimeImmutable::createFromFormat('Y-m-d', '2019-12-28');

        $this->createSeasonCommand = new CreateSeasonCommand(
            $this->sport,
            $this->seasonType,
            $this->name,
            $this->seasonStart->format('Y-m-d'),
            $this->seasonEnd->format('Y-m-d')
        );
    }

    public function testItAddsASeasonCreatedEventToTheSeasonRepository()
    {
        $sport = $this->sport;
        $seasonType = $this->seasonType;
        $name = $this->name;
        $seasonStart = $this->seasonStart;
        $seasonEnd = $this->seasonEnd;

        // only needs the add method
        $seasonRepository = $this->getMockBuilder(SeasonRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['add'])
            ->getMock();

        // the add method should be called once
        // the season object should have the SeasonCreated event
        $seasonRepository
            ->expects($this->once())
            ->method('add')
            ->with($this->callback(
                function ($season) use (
                $sport,
                $seasonType,
                $name,
                $seasonStart,
                $seasonEnd
            ) {
                    $events = $season->getRecordedEvents();

                    return $events[0] instanceof SeasonCreated
                && $events[0]->getAggregateId() instanceof SeasonId
                && $events[0]->getSport() === $sport
                && $events[0]->getSeasonType() === $seasonType
                && $events[0]->getName() === $name
                && $events[0]->getSeasonStart()->format('Y-m-d') === $seasonStart->format('Y-m-d')
                && $events[0]->getSeasonEnd()->format('Y-m-d') === $seasonEnd->format('Y-m-d')
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
                }
        ));

        $createSeasonHandler = new CreateSeasonHandler(
            $seasonRepository
        );

        $createSeasonHandler->handle($this->createSeasonCommand);
    }
}
