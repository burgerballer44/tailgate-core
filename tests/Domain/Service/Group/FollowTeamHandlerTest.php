<?php

namespace Tailgate\Test\Domain\Service\Group;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Group\FollowTeamCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Group\FollowId;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Group\TeamFollowed;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Team\Team;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Service\Group\FollowTeamHandler;

class FollowTeamHandlerTest extends TestCase
{
    private $groupId = 'groupId';
    private $teamId = 'teamId';
    private $seasonId = 'seasonId';
    private $designation = 'designation';
    private $mascot = 'mascot';
    private $followTeamCommand;
    private $group;
    private $team;
    private $season;

    public function setUp(): void
    {
        // create a group and clear events
        $this->group = Group::create(
            GroupId::fromString($this->groupId),
            'groupName',
            'code',
            UserId::fromString('userId')
        );
        $this->group->clearRecordedEvents();

        $this->followTeamCommand = new FollowTeamCommand($this->groupId, $this->teamId, $this->seasonId);
    }

    public function testItAttemptsToAddATeamFollowedEventToTheGroupRepository()
    {
        $groupId = $this->groupId;
        $teamId = $this->teamId;
        $seasonId = $this->seasonId;
        $group = $this->group;

        $groupRepository = $this->getMockBuilder(GroupRepositoryInterface::class)->getMock();

        // the get method should be called once and will return the team
        $groupRepository->expects($this->once())->method('get')->willReturn($group);

        // the add method should be called once
        // the group object should have the TeamFollowed event
        $groupRepository->expects($this->once())->method('add')->with($this->callback(
            function ($group) use ($teamId, $groupId, $seasonId) {
                $events = $group->getRecordedEvents();

                return $events[0] instanceof TeamFollowed
                && $events[0]->getAggregateId()->equals(GroupId::fromString($groupId))
                && $events[0]->getFollowId() instanceof FollowId
                && $events[0]->getTeamId()->equals(TeamId::fromString($teamId))
                && $events[0]->getSeasonId()->equals(SeasonId::fromString($seasonId))
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())->method('assert')->willReturn(true);

        $this->followTeamHandler = new FollowTeamHandler($validator, $groupRepository);

        $this->followTeamHandler->handle($this->followTeamCommand);
    }
}
