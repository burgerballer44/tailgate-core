<?php

namespace Tailgate\Test\Application\Command\Team;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Team\AddTeamCommand;
use Tailgate\Application\Command\Team\AddTeamHandler;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamAdded;
use Tailgate\Infrastructure\Persistence\Repository\TeamRepository;

class AddTeamHandlerTestTest extends TestCase
{
    private $designation = 'designation';
    private $mascot = 'mascot';
    private $addTeamCommand;

    public function setUp()
    {
        $this->addTeamCommand = new AddTeamCommand(
            $this->designation,
            $this->mascot
        );
    }

    public function testItAddsATeamAddedEventToTheTeamRepository()
    {
        $designation = $this->designation;
        $mascot = $this->mascot;

        // only needs the add method
        $teamRepository = $this->getMockBuilder(TeamRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['add'])
            ->getMock();

        // the add method should be called once
        // the team object should have the TeamAdded event
        $teamRepository
            ->expects($this->once())
            ->method('add')
            ->with($this->callback(function($team) use (
                $designation,
                $mascot
            ) {
                $events = $team->getRecordedEvents();

                return $events[0] instanceof TeamAdded
                && $events[0]->getAggregateId() instanceof TeamId
                && $events[0]->getDesignation() === $designation
                && $events[0]->getMascot() === $mascot
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $addTeamHandler = new AddTeamHandler(
            $teamRepository
        );

        $addTeamHandler->handle($this->addTeamCommand);
    }
}
