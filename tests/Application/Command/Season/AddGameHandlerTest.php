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
use Tailgate\Infrastructure\Persistence\Repository\SeasonRepository;

class AddGameHandlerTest extends TestCase
{
    private $seasonId = 'seasonId';
    private $homeTeamId = 'homeTeamId';
    private $awayTeamId = 'awayTeamId';
    private $startDate;

    private $sport = 'sport';
    private $seasonType = 'seasonType';
    private $name = 'name';
    private $seasonStart;
    private $seasonEnd;

    private $season;
    private $addGameCommand;

    public function setUp()
    {
        $this->seasonStart = \DateTimeImmutable::createFromFormat('Y-m-d', '2019-09-01');
        $this->seasonEnd = \DateTimeImmutable::createFromFormat('Y-m-d', '2019-12-28');

        $this->season = Season::create(
            SeasonId::fromString($this->seasonId),
            $this->sport,
            $this->seasonType,
            $this->name,
            $this->seasonStart,
            $this->seasonEnd
        );
        $this->season->clearRecordedEvents();
        $this->startDate = \DateTimeImmutable::createFromFormat('Y-m-d', '2019-10-01');

        $this->addGameCommand = new AddGameCommand(
            $this->seasonId,
            $this->homeTeamId,
            $this->awayTeamId,
            $this->startDate->format('Y-m-d')
        );
    }

    public function testItAddsAGameAddedEventToTheSeasonRepository()
    {
        $seasonId = $this->seasonId;
        $homeTeamId = $this->homeTeamId;
        $awayTeamId = $this->awayTeamId;
        $startDate = $this->startDate;
        $season = $this->season;

        // only needs the get and add method
        $seasonRepository = $this->getMockBuilder(SeasonRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'add'])
            ->getMock();

        // the get method should be called once and will return the group
        $seasonRepository
           ->expects($this->once())
           ->method('get')
           ->willReturn($season);

        // the add method should be called once
        // the season object should have the GameAdded event
        $seasonRepository
            ->expects($this->once())
            ->method('add')
            ->with($this->callback(function($season) use (
                $seasonId,
                $homeTeamId,
                $awayTeamId,
                $startDate
            ) {
                $events = $season->getRecordedEvents();

                return $events[0] instanceof GameAdded
                && $events[0]->getAggregateId() instanceof SeasonId
                && $events[0]->getGameId() instanceof GameId
                && $events[0]->getHomeTeamId()->equals(TeamId::fromString($homeTeamId))
                && $events[0]->getAwayTeamId()->equals(TeamId::fromString($awayTeamId))
                && $events[0]->getStartDate()->format('Y-m-d') === $startDate->format('Y-m-d')
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $addGameHandler = new AddGameHandler(
            $seasonRepository
        );

        $addGameHandler->handle($this->addGameCommand);
    }
}