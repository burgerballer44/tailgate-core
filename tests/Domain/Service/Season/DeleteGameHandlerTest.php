<?php

namespace Tailgate\Test\Domain\Service\Season;

use Tailgate\Application\Command\Season\DeleteGameCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Common\DateOrString;
use Tailgate\Domain\Model\Common\TimeOrString;
use Tailgate\Domain\Model\Season\GameDeleted;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;
use Tailgate\Domain\Model\Season\SeasonType;
use Tailgate\Domain\Model\Season\Sport;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Service\Clock\FakeClock;
use Tailgate\Domain\Service\Season\DeleteGameHandler;
use Tailgate\Test\BaseTestCase;

class DeleteGameHandlerTest extends BaseTestCase
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

        $this->deleteGameCommand = new DeleteGameCommand(
            $this->seasonId,
            $this->gameId
        );
    }

    public function testItAddsAGameDeletedEventToTheSeasonRepository()
    {
        $seasonRepository = $this->getMockBuilder(SeasonRepositoryInterface::class)->getMock();
        $seasonRepository->expects($this->once())->method('get')->willReturn($this->season);
        $seasonRepository->expects($this->once())->method('add');

        $deleteGameHandler = new DeleteGameHandler($seasonRepository, new FakeClock());

        $deleteGameHandler->handle($this->deleteGameCommand);
    }
}
