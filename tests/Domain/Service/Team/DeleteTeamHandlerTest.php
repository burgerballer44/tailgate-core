<?php

namespace Tailgate\Test\Domain\Service\Team;

use Tailgate\Application\Command\Team\DeleteTeamCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\Sport;
use Tailgate\Domain\Model\Team\Team;
use Tailgate\Domain\Model\Team\TeamDeleted;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamRepositoryInterface;
use Tailgate\Domain\Service\Clock\FakeClock;
use Tailgate\Domain\Service\Team\DeleteTeamHandler;
use Tailgate\Test\BaseTestCase;

class DeleteTeamHandlerTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->teamId = TeamId::fromString('teamId');
        $this->designation = 'designation';
        $this->mascot = 'mascot';
        $this->sport = Sport::getFootball();
        $this->dateOccurred = Date::fromDateTimeImmutable($this->getFakeTime()->currentTime());

        $this->team = Team::create($this->teamId, $this->designation, $this->mascot, $this->sport, $this->dateOccurred);
        $this->team->clearRecordedEvents();

        $this->deleteTeamCommand = new DeleteTeamCommand($this->teamId);
    }

    public function testItAttemptsToAddATeamDeletedEventToTheTeamRepository()
    {
        $teamRepository = $this->getMockBuilder(TeamRepositoryInterface::class)->getMock();
        $teamRepository->expects($this->once())->method('get')->willReturn($this->team);
        $teamRepository->expects($this->once())->method('add');

        $this->deleteTeamHandler = new DeleteTeamHandler($teamRepository, new FakeClock());

        $this->deleteTeamHandler->handle($this->deleteTeamCommand);
    }
}
