<?php

namespace Tailgate\Test\Application\Command\Season;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Season\AddGameScoreCommand;
use Tailgate\Application\Command\Season\AddGameScoreHandler;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\GameScoreAdded;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Infrastructure\Persistence\Repository\SeasonRepository;

class AddGameScoreHandlerTest extends TestCase
{
    private $homeTeamScore = 70;
    private $awayTeamScore = 60;

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
    private $game;
    private $addGameScoreCommand;

    public function setUp()
    {   
        // create season
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

        // add game
        $this->startDate = \DateTimeImmutable::createFromFormat('Y-m-d', '2019-10-01');
        $this->season->addGame(
            TeamId::fromString($this->homeTeamId),
            TeamId::fromString($this->awayTeamId),
            $this->startDate
        );
        $this->season->clearRecordedEvents();
        $games = $this->season->getGames();
        $this->game = $games[0];


        $this->addGameScoreCommand = new AddGameScoreCommand(
            SeasonId::fromString($this->seasonId),
            $this->game->getGameId(),
            $this->homeTeamScore,
            $this->awayTeamScore
        );
    }

    public function testItAddsAGameScoreAddedEventToTheSeasonRepository()
    {
        $homeTeamScore = $this->homeTeamScore;
        $awayTeamScore = $this->awayTeamScore;
        $season = $this->season;
        $game = $this->game;

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
        // the season object should have the GameScoreAdded event
        $seasonRepository
            ->expects($this->once())
            ->method('add')
            ->with($this->callback(function($season) use (
                $homeTeamScore,
                $awayTeamScore,
                $game
            ) {
                $events = $season->getRecordedEvents();

                return $events[0] instanceof GameScoreAdded
                && $events[0]->getAggregateId() instanceof SeasonId
                && $events[0]->getGameId()->equals($game->getGameId())
                && $events[0]->getHomeTeamScore() === $homeTeamScore
                && $events[0]->getAwayTeamScore() === $awayTeamScore
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $addGameScoreHandler = new AddGameScoreHandler(
            $seasonRepository
        );

        $addGameScoreHandler->handle($this->addGameScoreCommand);
    }
}
