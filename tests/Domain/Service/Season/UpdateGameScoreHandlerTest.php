<?php

namespace Tailgate\Test\Domain\Service\Season;

use Tailgate\Application\Command\Season\UpdateGameScoreCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Common\DateOrString;
use Tailgate\Domain\Model\Common\TimeOrString;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;
use Tailgate\Domain\Model\Season\SeasonType;
use Tailgate\Domain\Model\Season\Sport;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Service\Season\UpdateGameScoreHandler;
use Tailgate\Infrastructure\Service\Clock\FakeClock;
use Tailgate\Test\BaseTestCase;

class UpdateGameScoreHandlerTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->seasonId = SeasonId::fromString('seasonId');
        $this->name = 'name';
        $this->sport = Sport::getFootball();
        $this->seasonType = SeasonType::getRegularSeason();
        $this->seasonStart = DateOrString::fromString('2019-09-01');
        $this->seasonEnd = DateOrString::fromString('2019-12-28');
        $this->dateOccurred = Date::fromDateTimeImmutable($this->getFakeTime()->currentTime());
        $this->homeTeamId = TeamId::fromString('homeTeamId');
        $this->awayTeamId = TeamId::fromString('awayTeamId');
        $this->startDate = DateOrString::fromString('2019-10-01');
        $this->startTime = TimeOrString::fromString('19:30');
        $this->homeTeamScore = 70;
        $this->awayTeamScore = null;

        $this->season = Season::create(
            $this->seasonId,
            $this->name,
            $this->sport,
            $this->seasonType,
            $this->seasonStart,
            $this->seasonEnd,
            $this->dateOccurred
        );
        $this->season->addGame(
            $this->homeTeamId,
            $this->awayTeamId,
            $this->startDate,
            $this->startTime,
            $this->dateOccurred
        );
        $this->gameId = (string)$this->season->getGames()[0]->getGameId();
        $this->season->clearRecordedEvents();

        $this->updateGameScoreCommand = new UpdateGameScoreCommand(
            SeasonId::fromString($this->seasonId),
            $this->gameId,
            $this->homeTeamScore,
            $this->awayTeamScore,
            $this->startDate,
            $this->startTime
        );
    }

    public function testItAddsAGameScoreUpdatedEventToTheSeasonRepository()
    {
        $seasonRepository = $this->getMockBuilder(SeasonRepositoryInterface::class)->getMock();
        $seasonRepository->expects($this->once())->method('get')->willReturn($this->season);
        $seasonRepository->expects($this->once())->method('add');

        $updateGameScoreHandler = new UpdateGameScoreHandler(new FakeClock(), $seasonRepository);

        $updateGameScoreHandler->handle($this->updateGameScoreCommand);
    }
}
