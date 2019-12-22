<?php

namespace Tailgate\Test\Domain\Service\Team;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Team\AddTeamCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Team\TeamAdded;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamRepositoryInterface;
use Tailgate\Domain\Service\Team\AddTeamHandler;

class AddTeamHandlerTest extends TestCase
{
    private $designation = 'designation';
    private $mascot = 'mascot';
    private $addTeamCommand;

    public function setUp()
    {
        $this->addTeamCommand = new AddTeamCommand($this->designation, $this->mascot);
    }

    public function testItAddsATeamAddedEventToTheTeamRepository()
    {
        $designation = $this->designation;
        $mascot = $this->mascot;

        $teamRepository = $this->getMockBuilder(TeamRepositoryInterface::class)->getMock();

        // the nextIdentity method should be called once and will return a new TeamID
        $teamRepository->expects($this->once())->method('nextIdentity')->willReturn(new TeamId());

        // the add method should be called once
        // the team object should have the TeamAdded event
        $teamRepository->expects($this->once())->method('add')->with($this->callback(
            function ($team) use ($designation, $mascot) {
                $events = $team->getRecordedEvents();

                return $events[0] instanceof TeamAdded
                && $events[0]->getAggregateId() instanceof TeamId
                && $events[0]->getDesignation() === $designation
                && $events[0]->getMascot() === $mascot
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())->method('assert')->willReturn(true);

        $addTeamHandler = new AddTeamHandler($validator, $teamRepository);

        $addTeamHandler->handle($this->addTeamCommand);
    }
}
