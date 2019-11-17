<?php

namespace Tailgate\Test\Application\Command\Team;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Team\FollowTeamCommand;
use Tailgate\Application\Command\Team\FollowTeamHandler;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Team\FollowId;
use Tailgate\Domain\Model\Team\TeamFollowed;
use Tailgate\Domain\Model\Team\Team;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamRepositoryInterface;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\SeasonId;

class FollowTeamHandlerTest extends TestCase
{
    private $groupId = 'groupId';
    private $teamId = 'teamId';
    private $seasonId = 'seasonId';
    private $designation = 'designation';
    private $mascot = 'mascot';
    private $followTeamCommand;
    private $team;
    private $season;

    public function setUp()
    {
        // create a team and clear events
        $this->team = Team::create(TeamId::fromString($this->teamId), $this->designation, $this->mascot);
        $this->team->clearRecordedEvents();

        // create a season and clear events
        $this->season = Season::create(
            SeasonId::fromString($this->seasonId),
            'football season',
            Season::SPORT_FOOTBALL,
            Season::SEASON_TYPE_REG,
            \DateTimeImmutable::createFromFormat('Y-m-d', '2019-09-01'),
            \DateTimeImmutable::createFromFormat('Y-m-d', '2019-12-28')
        );
        $this->season->clearRecordedEvents();

        $this->followTeamCommand = new FollowTeamCommand($this->groupId, $this->teamId, $this->seasonId);
    }

    public function testItAttemptsToAddATeamFollowedEventToTheTeamRepository()
    {
        $groupId = $this->groupId;
        $teamId = $this->teamId;
        $seasonId = $this->seasonId;
        $team = $this->team;
        $season = $this->season;

        $teamRepository = $this->getMockBuilder(TeamRepositoryInterface::class)->getMock();

        // the get method should be called once and will return the team
        $teamRepository->expects($this->once())->method('get')->willReturn($team);

        // the add method should be called once
        // the team object should have the TeamFollowed event
        $teamRepository->expects($this->once())->method('add')->with($this->callback(
            function ($team) use ($teamId, $groupId, $seasonId) {
                $events = $team->getRecordedEvents();

                return $events[0] instanceof TeamFollowed
                && $events[0]->getAggregateId()->equals(TeamId::fromString($teamId))
                && $events[0]->getFollowId() instanceof FollowId
                && $events[0]->getGroupId()->equals(GroupId::fromString($groupId))
                && $events[0]->getSeasonId()->equals(SeasonId::fromString($seasonId))
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $this->followTeamHandler = new FollowTeamHandler($teamRepository);

        $this->followTeamHandler->handle($this->followTeamCommand);
    }
}
