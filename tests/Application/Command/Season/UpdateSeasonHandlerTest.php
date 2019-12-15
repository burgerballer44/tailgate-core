<?php

namespace Tailgate\Test\Application\Command\Season;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Season\UpdateSeasonCommand;
use Tailgate\Application\Command\Season\UpdateSeasonHandler;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonUpdated;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;

class UpdateSeasonHandlerTest extends TestCase
{
    private $seasonId = 'seasonId';
    private $sport = Season::SPORT_FOOTBALL;
    private $seasonType = Season::SEASON_TYPE_REG;
    private $name = 'updatedname';
    private $seasonStart;
    private $seasonEnd;
    private $season;
    private $updateSeasonCommand;

    public function setUp()
    {
        // create season and clear events
        $this->seasonStart = '2021-09-01';
        $this->seasonEnd = '2021-12-28';
        $this->season = Season::create(
            SeasonId::fromString($this->seasonId),
            'Name',
            Season::SPORT_FOOTBALL,
            Season::SEASON_TYPE_REG,
            '2019-09-01',
            '2019-09-01'
        );
        $this->season->clearRecordedEvents();

        $this->updateSeasonCommand = new UpdateSeasonCommand(
            SeasonId::fromString($this->seasonId),
            $this->name,
            $this->sport,
            $this->seasonType,
            $this->seasonStart,
            $this->seasonEnd
        );
    }

    public function testItAddsASeasonUpdatedEventToTheSeasonRepository()
    {
        $seasonId = $this->seasonId;
        $sport = $this->sport;
        $seasonType = $this->seasonType;
        $name = $this->name;
        $seasonStart = $this->seasonStart;
        $seasonEnd = $this->seasonEnd;
        $season = $this->season;

        $seasonRepository = $this->getMockBuilder(SeasonRepositoryInterface::class)->getMock();

        // the nextIdentity method should be called once and will return a the season
        $seasonRepository->expects($this->once())->method('get')->willReturn($season);

        // the add method should be called once
        // the season object should have the SeasonUpdated event
        $seasonRepository->expects($this->once())->method('add')->with($this->callback(
            function ($season) use ($seasonId, $sport, $seasonType, $name, $seasonStart, $seasonEnd) {
                $events = $season->getRecordedEvents();

                return $events[0] instanceof SeasonUpdated
                && $events[0]->getAggregateId()->equals(SeasonId::fromString($seasonId))
                && $events[0]->getSport() === $sport
                && $events[0]->getSeasonType() === $seasonType
                && $events[0]->getName() === $name
                && $events[0]->getSeasonStart() === \DateTimeImmutable::createFromFormat('Y-m-d', $seasonStart)->format('Y-m-d H:i:s')
                && $events[0]->getSeasonEnd() === \DateTimeImmutable::createFromFormat('Y-m-d', $seasonEnd)->format('Y-m-d H:i:s')
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $updateSeasonHandler = new UpdateSeasonHandler($seasonRepository);

        $updateSeasonHandler->handle($this->updateSeasonCommand);
    }
}
