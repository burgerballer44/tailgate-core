<?php

namespace Tailgate\Test\Application\Command\Season;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Season\CreateSeasonCommand;
use Tailgate\Application\Command\Season\CreateSeasonHandler;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonCreated;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;

class CreateSeasonHandlerTest extends TestCase
{
    private $name = 'name';
    private $sport = Season::SPORT_FOOTBALL;
    private $seasonType = Season::SEASON_TYPE_REG;
    private $seasonStart;
    private $seasonEnd;
    private $createSeasonCommand;

    public function setUp()
    {
        $this->seasonStart = '2019-09-01';
        $this->seasonEnd = '2019-12-28';

        $this->createSeasonCommand = new CreateSeasonCommand(
            $this->name,
            $this->sport,
            $this->seasonType,
            $this->seasonStart,
            $this->seasonEnd
        );
    }

    public function testItAddsASeasonCreatedEventToTheSeasonRepository()
    {
        $name = $this->name;
        $sport = $this->sport;
        $seasonType = $this->seasonType;
        $seasonStart = $this->seasonStart;
        $seasonEnd = $this->seasonEnd;

        $seasonRepository = $this->getMockBuilder(SeasonRepositoryInterface::class)->getMock();

        // the nextIdentity method should be called once and will return a new SeasonID
        $seasonRepository->expects($this->once())->method('nextIdentity')->willReturn(new SeasonId());

        // the add method should be called once
        // the season object should have the SeasonCreated event
        $seasonRepository->expects($this->once())->method('add')->with($this->callback(
            function ($season) use ($name, $seasonType, $sport, $seasonStart, $seasonEnd) {
                $events = $season->getRecordedEvents();

                return $events[0] instanceof SeasonCreated
                && $events[0]->getAggregateId() instanceof SeasonId
                && $events[0]->getName() === $name
                && $events[0]->getSport() === $sport
                && $events[0]->getSeasonType() === $seasonType
                && $events[0]->getSeasonStart() === \DateTimeImmutable::createFromFormat('Y-m-d', $seasonStart)->format('Y-m-d H:i:s')
                && $events[0]->getSeasonEnd() === \DateTimeImmutable::createFromFormat('Y-m-d', $seasonEnd)->format('Y-m-d H:i:s')
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $createSeasonHandler = new CreateSeasonHandler($seasonRepository);

        $createSeasonHandler->handle($this->createSeasonCommand);
    }
}
