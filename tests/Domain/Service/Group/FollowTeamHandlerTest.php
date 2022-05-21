<?php

namespace Tailgate\Test\Domain\Service\Group;

use Tailgate\Application\Command\Group\FollowTeamCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupInviteCode;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Service\Clock\FakeClock;
use Tailgate\Domain\Service\Group\FollowTeamHandler;
use Tailgate\Test\BaseTestCase;

class FollowTeamHandlerTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->groupId = GroupId::fromString('groupId');
        $this->userId = UserId::fromString('userId');
        $this->teamId = TeamId::fromString('teamId');
        $this->seasonId = SeasonId::fromString('seasonId');
        $this->groupName = 'groupName';
        $this->groupInviteCode = GroupInviteCode::create();
        $this->dateOccurred = Date::fromDateTimeImmutable($this->getFakeTime()->currentTime());

        // create a group and clear events
        $this->group = Group::create(
            $this->groupId,
            $this->groupName,
            $this->groupInviteCode,
            $this->userId,
            $this->dateOccurred
        );
        $this->group->clearRecordedEvents();

        $this->followTeamCommand = new FollowTeamCommand($this->groupId, $this->teamId, $this->seasonId);
    }

    public function testItAttemptsToAddATeamFollowedEventToTheGroupRepository()
    {
        $groupRepository = $this->getMockBuilder(GroupRepositoryInterface::class)->getMock();
        $groupRepository->expects($this->once())->method('get')->willReturn($this->group);
        $groupRepository->expects($this->once())->method('add');

        $this->followTeamHandler = new FollowTeamHandler(new FakeClock(), $groupRepository);

        $this->followTeamHandler->handle($this->followTeamCommand);
    }
}
