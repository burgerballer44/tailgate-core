<?php

namespace Tailgate\Test\Domain\Model;

use Burger\Aggregate\AggregateHistory;
use RuntimeException;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Common\DateOrString;
use Tailgate\Domain\Model\Common\TimeOrString;
use Tailgate\Domain\Model\Season\Game;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonType;
use Tailgate\Domain\Model\Season\Sport;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Test\BaseTestCase;

class SeasonTest extends BaseTestCase
{
    private function createSeason()
    {
        return Season::create(
            $this->seasonId,
            $this->name,
            $this->sport,
            $this->seasonType,
            $this->seasonStart,
            $this->seasonEnd,
            $this->dateOccurred
        );
    }

    public function setUp(): void
    {
        $this->seasonId = SeasonId::fromString('seasonId');
        $this->seasonStart = DateOrString::fromString('2019-09-01');
        $this->seasonEnd = DateOrString::fromString('2019-12-28');
        $this->sport = Sport::getFootball();
        $this->seasonType = SeasonType::getRegularSeason();
        $this->name = 'name';
        $this->dateOccurred = Date::fromDateTimeImmutable($this->getFakeTime()->currentTime());
    }

    public function testSeasonShouldBeTheSameAfterReconstitution()
    {
        // create a ceason
        $season = $this->createSeason();
        $events = $season->getRecordedEvents();
        $season->clearRecordedEvents();

        // recreate the season using event array
        $reconstitutedSeason = Season::reconstituteFromEvents(
            new AggregateHistory($this->seasonId, (array) $events)
        );

        // both season objects should be the same
        $this->assertEquals(
            $season,
            $reconstitutedSeason,
            'the reconstituted season does not match the original season'
        );
    }

    public function testASeasonCanBeCreated()
    {
        $season = $this->createSeason();

        $seasonCreatedEvent = $season->getRecordedEvents()[0];

        $this->assertEquals($this->seasonId, $seasonCreatedEvent->getAggregateId());
        $this->assertEquals($this->sport, $seasonCreatedEvent->getSport());
        $this->assertEquals($this->seasonType, $seasonCreatedEvent->getSeasonType());
        $this->assertEquals($this->name, $seasonCreatedEvent->getName());
        $this->assertEquals($this->seasonStart, $seasonCreatedEvent->getSeasonStart());
        $this->assertEquals($this->seasonEnd, $seasonCreatedEvent->getSeasonEnd());
        $this->assertEquals($this->dateOccurred, $seasonCreatedEvent->getDateOccurred());
        $this->assertEquals($this->seasonId, $season->getSeasonId());
        $this->assertEquals($this->sport, $season->getSport());
        $this->assertEquals($this->seasonType, $season->getSeasonType());
        $this->assertEquals($this->name, $season->getName());
        $this->assertEquals($this->seasonStart, $season->getSeasonStart());
        $this->assertEquals($this->seasonEnd, $season->getSeasonEnd());
    }

    public function testASeasonHasNoGamesWhenCreated()
    {
        $season = $this->createSeason();

        $this->assertCount(0, $season->getGames());
    }

    public function testAGameCanBeAddedToASeason()
    {
        $season = $this->createSeason();
        $season->clearRecordedEvents();

        $homeTeamId = TeamId::fromString('homeTeamId');
        $awayTeamId = TeamId::fromString('awayTeamId');
        $startDate = DateOrString::fromString('2019-10-01');
        $startTime = TimeOrString::fromString('12:12');
        $season->addGame($homeTeamId, $awayTeamId, $startDate, $startTime, $this->dateOccurred);

        $gameAddedEvent = $season->getRecordedEvents()[0];
        $games = $season->getGames();

        $this->assertTrue($gameAddedEvent->getHomeTeamId()->equals($homeTeamId));
        $this->assertTrue($gameAddedEvent->getAwayTeamId()->equals($awayTeamId));
        $this->assertEquals($startDate, $gameAddedEvent->getStartDate());
        $this->assertEquals($startTime, $gameAddedEvent->getStartTime());
        $this->assertEquals($this->dateOccurred, $gameAddedEvent->getDateOccurred());
        $this->assertTrue($games[0]->getSeasonId()->equals($season->getSeasonId()));
        $this->assertTrue($games[0]->getHomeTeamId()->equals($homeTeamId));
        $this->assertTrue($games[0]->getAwayTeamId()->equals($awayTeamId));
        $this->assertEquals($startDate, $games[0]->getStartDate());
        $this->assertEquals($startTime, $games[0]->getStartTime());
    }

    public function testAGameScoreCanBeUpdated()
    {
        $season = $this->createSeason();
        $season->clearRecordedEvents();
        $homeTeamId = TeamId::fromString('homeTeamId');
        $awayTeamId = TeamId::fromString('awayTeamId');
        $startDate = DateOrString::fromString('2019-10-01');
        $startTime = TimeOrString::fromString('12:12');
        $season->addGame($homeTeamId, $awayTeamId, $startDate, $startTime, $this->dateOccurred);
        $gameAddedEvent = $season->getRecordedEvents()[0];
        $season->clearRecordedEvents();

        $homeTeamScore = 70;
        $awayTeamScore = 60;
        $newStartDate = DateOrString::fromString('2019-12-01');
        $newStartTime = TimeOrString::fromString('19:30');
        $season->updateGameScore(
            $gameAddedEvent->getGameId(),
            $homeTeamScore,
            $awayTeamScore,
            $newStartDate,
            $newStartTime,
            $this->dateOccurred
        );

        $gameScoreUpdatedEvent = $season->getRecordedEvents()[0];
        $games = $season->getGames();

        $this->assertEquals($homeTeamScore, $gameScoreUpdatedEvent->getHomeTeamScore());
        $this->assertEquals($awayTeamScore, $gameScoreUpdatedEvent->getAwayTeamScore());
        $this->assertEquals($newStartDate, $gameScoreUpdatedEvent->getStartDate());
        $this->assertEquals($newStartTime, $gameScoreUpdatedEvent->getStartTime());
        $this->assertEquals($this->dateOccurred, $gameScoreUpdatedEvent->getDateOccurred());
        $this->assertEquals($homeTeamScore, $games[0]->getHomeTeamScore());
        $this->assertEquals($awayTeamScore, $games[0]->getAwayTeamScore());
        $this->assertEquals($newStartDate, $games[0]->getStartDate());
        $this->assertEquals($newStartTime, $games[0]->getStartTime());
    }

    public function testExceptionThrownWhenTryingToUpdateScoreForGameThatDoesNotExist()
    {
        $season = $this->createSeason();
        $homeTeamId = TeamId::fromString('homeTeamId');
        $awayTeamId = TeamId::fromString('awayTeamId');
        $startDate = DateOrString::fromString('2019-10-01');
        $startTime = TimeOrString::fromString('12:12');
        $season->addGame($homeTeamId, $awayTeamId, $startDate, $startTime, $this->dateOccurred);
        $season->clearRecordedEvents();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The game does not exist. Cannot update the game score.');
        $season->updateGameScore(
            GameId::fromString('gameThatDoesNoExist'),
            12,
            23,
            DateOrString::fromString('2019-12-01'),
            TimeOrString::fromString('12:12'),
            $this->dateOccurred
        );
    }

    public function testAGameCanBeRemovedFromASeason()
    {
        // create a season and add three games
        $season = $this->createSeason();
        $homeTeamId = TeamId::fromString('homeTeamId');
        $awayTeamId = TeamId::fromString('awayTeamId');
        $startDate = DateOrString::fromString('2019-10-01');
        $startTime = TimeOrString::fromString('12:12');
        $season->addGame($homeTeamId, $awayTeamId, $startDate, $startTime, $this->dateOccurred);
        $season->addGame($homeTeamId, $awayTeamId, $startDate, $startTime, $this->dateOccurred);
        $season->addGame($homeTeamId, $awayTeamId, $startDate, $startTime, $this->dateOccurred);
        $gameAddedEvent1 = $season->getRecordedEvents()[1];
        $gameAddedEvent2 = $season->getRecordedEvents()[2];
        $gameAddedEvent3 = $season->getRecordedEvents()[3];
        $season->clearRecordedEvents();

        $gameId1 = $gameAddedEvent1->getGameId();
        $gameId2 = $gameAddedEvent2->getGameId();
        $gameId3 = $gameAddedEvent3->getGameId();

        // remove the second game
        $season->deleteGame($gameId2, $this->dateOccurred);

        $gameDeletedEvent = $season->getRecordedEvents()[0];
        $games = $season->getGames();

        $this->assertTrue($gameId2->equals($gameDeletedEvent->getGameId()));
        $this->assertEquals($this->dateOccurred, $gameDeletedEvent->getDateOccurred());
        $this->assertTrue($gameId1->equals($games[0]->getGameId()));
        $this->assertTrue($gameId3->equals($games[1]->getGameId()));
    }

    public function testExceptionThrownWhenDeletingGameThatDoesNotExist()
    {
        // create a season and add three games
        $season = $this->createSeason();
        $homeTeamId = TeamId::fromString('homeTeamId');
        $awayTeamId = TeamId::fromString('awayTeamId');
        $startDate = DateOrString::fromString('2019-10-01');
        $startTime = TimeOrString::fromString('12:12');
        $season->addGame($homeTeamId, $awayTeamId, $startDate, $startTime, $this->dateOccurred);
        $season->addGame($homeTeamId, $awayTeamId, $startDate, $startTime, $this->dateOccurred);
        $season->addGame($homeTeamId, $awayTeamId, $startDate, $startTime, $this->dateOccurred);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The game does not exist. Cannot delete the game.');
        $season->deleteGame(GameId::fromString('gameThatDoesNoExist'), $this->dateOccurred);
    }

    public function testThereAreNoGamesInADeletedSeason()
    {
        // create a season and add a game
        $season = $this->createSeason();

        $homeTeamId = TeamId::fromString('homeTeamId');
        $awayTeamId = TeamId::fromString('awayTeamId');
        $startDate = DateOrString::fromString('2019-10-01');
        $startTime = TimeOrString::fromString('12:12');
        $season->addGame($homeTeamId, $awayTeamId, $startDate, $startTime, $this->dateOccurred);

        $games = $season->getGames();
        $this->assertCount(1, $games);

        $season->delete($this->dateOccurred);

        $games = $season->getGames();

        $this->assertCount(0, $games);
    }

    public function testASeasonCanBeUpdated()
    {
        $sport = Sport::getFootball();
        $seasonType = SeasonType::getRegularSeason();
        $name = 'updatedname';
        $seasonStart = DateOrString::fromString('2021-12-28');
        $seasonEnd = DateOrString::fromString('2021-12-28');
        $season = $this->createSeason();
        $season->clearRecordedEvents();

        $season->update($sport, $seasonType, $name, $seasonStart, $seasonEnd, $this->dateOccurred);

        $seasonUpdatedEvent = $season->getRecordedEvents()[0];

        $this->assertEquals($sport, $seasonUpdatedEvent->getSport());
        $this->assertEquals($seasonType, $seasonUpdatedEvent->getSeasonType());
        $this->assertEquals($name, $seasonUpdatedEvent->getName());
        $this->assertEquals($seasonStart, $seasonUpdatedEvent->getSeasonStart());
        $this->assertEquals($seasonEnd, $seasonUpdatedEvent->getSeasonEnd());
        $this->assertEquals($this->dateOccurred, $seasonUpdatedEvent->getDateOccurred());
        $this->assertEquals($sport, $season->getSport());
        $this->assertEquals($seasonType, $season->getSeasonType());
        $this->assertEquals($name, $season->getName());
        $this->assertEquals($seasonStart, $season->getSeasonStart());
        $this->assertEquals($seasonEnd, $season->getSeasonEnd());
    }
}
