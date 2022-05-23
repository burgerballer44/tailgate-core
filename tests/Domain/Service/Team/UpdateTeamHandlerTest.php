<?php

namespace Tailgate\Test\Domain\Service\Team;

use Tailgate\Application\Command\Team\UpdateTeamCommand;

use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Season\Sport;
use Tailgate\Domain\Model\Team\Team;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamRepositoryInterface;
use Tailgate\Domain\Service\Team\UpdateTeamHandler;
use Tailgate\Infrastructure\Service\Clock\FakeClock;
use Tailgate\Test\BaseTestCase;

class UpdateTeamHandlerTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->teamId = TeamId::fromString('teamId');
        $this->designation = 'designation';
        $this->mascot = 'mascot';
        $this->sport = Sport::getFootball();
        $this->updatedDesignation = 'designation';
        $this->updatedMascot = 'mascot';
        $this->dateOccurred = Date::fromDateTimeImmutable($this->getFakeTime()->currentTime());

        $this->team = Team::create($this->teamId, $this->designation, $this->mascot, $this->sport, $this->dateOccurred);
        $this->team->clearRecordedEvents();

        $this->updateTeamCommand = new UpdateTeamCommand($this->teamId, $this->updatedDesignation, $this->updatedMascot);
    }

    public function testItAttemptsToAddATeamUpdatedEventToTheTeamRepository()
    {
        $teamRepository = $this->getMockBuilder(TeamRepositoryInterface::class)->getMock();
        $teamRepository->expects($this->once())->method('get')->willReturn($this->team);
        $teamRepository->expects($this->once())->method('add');

        $this->updateTeamHandler = new UpdateTeamHandler(new FakeClock(), $teamRepository);

        $this->updateTeamHandler->handle($this->updateTeamCommand);
    }
}
