<?php

namespace Tailgate\Test\Domain\Service\Season;

use Tailgate\Application\Command\Season\UpdateSeasonCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Common\DateOrString;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;
use Tailgate\Domain\Model\Season\SeasonType;
use Tailgate\Domain\Model\Season\Sport;
use Tailgate\Domain\Service\Clock\FakeClock;
use Tailgate\Domain\Service\Season\UpdateSeasonHandler;
use Tailgate\Test\BaseTestCase;

class UpdateSeasonHandlerTest extends BaseTestCase
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

        // create a season and clear events
        $this->season = Season::create(
            $this->seasonId,
            $this->name,
            $this->sport,
            $this->seasonType,
            $this->seasonStart,
            $this->seasonEnd,
            $this->dateOccurred
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
        $seasonRepository = $this->getMockBuilder(SeasonRepositoryInterface::class)->getMock();
        $seasonRepository->expects($this->once())->method('get')->willReturn($this->season);
        $seasonRepository->expects($this->once())->method('add');

        $updateSeasonHandler = new UpdateSeasonHandler(new FakeClock(), $seasonRepository);

        $updateSeasonHandler->handle($this->updateSeasonCommand);
    }
}
