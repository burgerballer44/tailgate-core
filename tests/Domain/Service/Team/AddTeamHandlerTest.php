<?php

namespace Tailgate\Test\Domain\Service\Team;

use Tailgate\Application\Command\Team\AddTeamCommand;

use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Season\Sport;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamRepositoryInterface;
use Tailgate\Domain\Service\Team\AddTeamHandler;
use Tailgate\Infrastructure\Service\Clock\FakeClock;
use Tailgate\Test\BaseTestCase;

class AddTeamHandlerTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->designation = 'designation';
        $this->mascot = 'mascot';
        $this->sport = Sport::getFootball();
        $this->dateOccurred = Date::fromDateTimeImmutable($this->getFakeTime()->currentTime());

        $this->addTeamCommand = new AddTeamCommand($this->designation, $this->mascot, $this->sport, $this->dateOccurred);
    }

    public function testItAddsATeamAddedEventToTheTeamRepository()
    {
        $teamRepository = $this->getMockBuilder(TeamRepositoryInterface::class)->getMock();
        $teamRepository->expects($this->once())->method('nextIdentity')->willReturn(new TeamId());
        $teamRepository->expects($this->once())->method('add');

        $addTeamHandler = new AddTeamHandler(new FakeClock(), $teamRepository);

        $addTeamHandler->handle($this->addTeamCommand);
    }
}
