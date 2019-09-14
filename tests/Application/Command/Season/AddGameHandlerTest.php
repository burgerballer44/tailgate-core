<?php

namespace Tailgate\Test\Application\Command\Season;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Season\AddGameCommand;
use Tailgate\Application\Command\Season\AddGameHandler;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\GameAdded;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;

class AddGameHandlerTest extends TestCase
{
    private $seasonId = 'seasonId';
    private $homeTeamId = 'homeTeamId';
    private $awayTeamId = 'awayTeamId';
    private $startDate;

    private $name = 'name';
    private $sport = Season::SPORT_FOOTBALL;
    private $seasonType = Season::SEASON_TYPE_REG;
    private $seasonStart;
    private $seasonEnd;

    private $season;
    private $addGameCommand;

    public function setUp()
    {
        $this->seasonStart = \DateTimeImmutable::createFromFormat('Y-m-d', '2019-09-01');
        $this->seasonEnd = \DateTimeImmutable::createFromFormat('Y-m-d', '2019-12-28');

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

        $this->startDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2019-10-01 19:30:13');
        $this->addGameCommand = new AddGameCommand(
            $this->seasonId,
            $this->homeTeamId,
            $this->awayTeamId,
            $this->startDate->format('Y-m-d H:i:s')
        );
    }

    public function testItAddsAGameAddedEventToTheSeasonRepository()
    {
        $seasonId = $this->seasonId;
        $homeTeamId = $this->homeTeamId;
        $awayTeamId = $this->awayTeamId;
        $startDate = $this->startDate;
        $season = $this->season;

        $seasonRepository = $this->getMockBuilder(SeasonRepositoryInterface::class)->getMock();

        // the get method should be called once and will return the group
        $seasonRepository->expects($this->once())->method('get')->willReturn($season);

        // the add method should be called once
        // the season object should have the GameAdded event
        $seasonRepository->expects($this->once())->method('add')->with($this->callback(
            function ($season) use ($seasonId, $homeTeamId, $awayTeamId, $startDate) {
                $events = $season->getRecordedEvents();

                return $events[0] instanceof GameAdded
                && $events[0]->getAggregateId()->equals(SeasonId::fromString($seasonId))
                && $events[0]->getGameId() instanceof GameId
                && $events[0]->getHomeTeamId()->equals(TeamId::fromString($homeTeamId))
                && $events[0]->getAwayTeamId()->equals(TeamId::fromString($awayTeamId))
                && $events[0]->getStartDate()->format('Y-m-d') === $startDate->format('Y-m-d')
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $addGameHandler = new AddGameHandler($seasonRepository);

        $addGameHandler->handle($this->addGameCommand);
    }
}
