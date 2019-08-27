<?php

namespace Tailgate\Test\Application\Command\Season;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Season\UpdateGameScoreCommand;
use Tailgate\Application\Command\Season\UpdateGameScoreHandler;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\GameScoreUpdated;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;

class UpdateGameScoreHandlerTest extends TestCase
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
    private $UpdateGameScoreCommand;

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


        $this->UpdateGameScoreCommand = new UpdateGameScoreCommand(
            SeasonId::fromString($this->seasonId),
            $this->game->getGameId(),
            $this->homeTeamScore,
            $this->awayTeamScore
        );
    }

    public function testItAddsAGameScoreUpdatedEventToTheSeasonRepository()
    {
        $homeTeamScore = $this->homeTeamScore;
        $awayTeamScore = $this->awayTeamScore;
        $season = $this->season;
        $game = $this->game;

        $seasonRepository = $this->getMockBuilder(SeasonRepositoryInterface::class)->getMock();

        // the get method should be called once and will return the group
        $seasonRepository
           ->expects($this->once())
           ->method('get')
           ->willReturn($season);

        // the add method should be called once
        // the season object should have the GameScoreUpdated event
        $seasonRepository
            ->expects($this->once())
            ->method('add')
            ->with($this->callback(
                function ($season) use (
                $homeTeamScore,
                $awayTeamScore,
                $game
            ) {
                    $events = $season->getRecordedEvents();

                    return $events[0] instanceof GameScoreUpdated
                && $events[0]->getAggregateId() instanceof SeasonId
                && $events[0]->getGameId()->equals($game->getGameId())
                && $events[0]->getHomeTeamScore() === $homeTeamScore
                && $events[0]->getAwayTeamScore() === $awayTeamScore
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
                }
        ));

        $UpdateGameScoreHandler = new UpdateGameScoreHandler(
            $seasonRepository
        );

        $UpdateGameScoreHandler->handle($this->UpdateGameScoreCommand);
    }
}
