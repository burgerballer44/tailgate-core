<?php

namespace Tailgate\Test\Domain\Service\Season;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Season\AddGameCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Season\GameAdded;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Service\Season\AddGameHandler;

class AddGameHandlerTest extends TestCase
{
    private $seasonId = 'seasonId';
    private $homeTeamId = 'homeTeamId';
    private $awayTeamId = 'awayTeamId';
    private $startDate;
    private $startTime;

    private $name = 'name';
    private $sport = Season::SPORT_FOOTBALL;
    private $seasonType = Season::SEASON_TYPE_REG;
    private $seasonStart;
    private $seasonEnd;

    private $season;
    private $addGameCommand;

    public function setUp(): void
    {
        $this->seasonStart = '2019-09-01';
        $this->seasonEnd = '2019-12-28';

        // create a season and clear events
        $this->season = Season::create(
            SeasonId::fromString($this->seasonId),
            $this->name,
            $this->sport,
            $this->seasonType,
            $this->seasonStart,
            $this->seasonEnd
        );
        $this->season->clearRecordedEvents();

        $this->startDate = '2019-10-01';
        $this->startTime = '19:30';
        $this->addGameCommand = new AddGameCommand(
            $this->seasonId,
            $this->homeTeamId,
            $this->awayTeamId,
            $this->startDate,
            $this->startTime
        );
    }

    public function testItAddsAGameAddedEventToTheSeasonRepository()
    {
        $seasonId = $this->seasonId;
        $homeTeamId = $this->homeTeamId;
        $awayTeamId = $this->awayTeamId;
        $startDate = $this->startDate;
        $startTime = $this->startTime;
        $season = $this->season;

        $seasonRepository = $this->getMockBuilder(SeasonRepositoryInterface::class)->getMock();

        // the get method should be called once and will return the group
        $seasonRepository->expects($this->once())->method('get')->willReturn($season);

        // the add method should be called once
        // the season object should have the GameAdded event
        $seasonRepository->expects($this->once())->method('add')->with($this->callback(
            function ($season) use ($seasonId, $homeTeamId, $awayTeamId, $startDate, $startTime) {
                $events = $season->getRecordedEvents();

                return $events[0] instanceof GameAdded
                && $events[0]->getAggregateId()->equals(SeasonId::fromString($seasonId))
                && $events[0]->getGameId() instanceof GameId
                && $events[0]->getHomeTeamId()->equals(TeamId::fromString($homeTeamId))
                && $events[0]->getAwayTeamId()->equals(TeamId::fromString($awayTeamId))
                && $events[0]->getStartDate() === \DateTimeImmutable::createFromFormat('Y-m-d', $startDate)->format('Y-m-d H:i:s')
                && $events[0]->getStartTime() === \DateTimeImmutable::createFromFormat('H:i', $startTime)->format('Y-m-d H:i:s')
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())->method('assert')->willReturn(true);

        $addGameHandler = new AddGameHandler($validator, $seasonRepository);

        $addGameHandler->handle($this->addGameCommand);
    }
}
