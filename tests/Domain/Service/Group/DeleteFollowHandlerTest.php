<?php

namespace Tailgate\Test\Domain\Service\Group;

use Tailgate\Application\Command\Group\DeleteFollowCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupInviteCode;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Service\Clock\FakeClock;
use Tailgate\Domain\Service\Group\DeleteFollowHandler;
use Tailgate\Test\BaseTestCase;

class DeleteFollowHandlerTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->groupId = GroupId::fromString('groupId');
        $this->userId = UserId::fromString('userId');
        $this->groupName = 'groupName';
        $this->groupInviteCode = GroupInviteCode::create();
        $this->teamId = TeamId::fromString('teamId');
        $this->seasonId = SeasonId::fromString('seasonId');
        $this->dateOccurred = Date::fromDateTimeImmutable($this->getFakeTime()->currentTime());

        // create a group and clear events
        $this->group = Group::create(
            $this->groupId,
            $this->groupName,
            $this->groupInviteCode,
            $this->userId,
            $this->dateOccurred
        );
        $this->group->followTeam($this->teamId, $this->seasonId, $this->dateOccurred);
        $this->group->clearRecordedEvents();

        $this->followId = (string) $this->group->getFollow()->getFollowId();

        $this->deleteFollowCommand = new DeleteFollowCommand($this->groupId, $this->followId);
    }

    public function testItAttemptsToAddAFollowDeletedEventToTheGroupRepository()
    {
        $groupRepository = $this->getMockBuilder(GroupRepositoryInterface::class)->getMock();
        $groupRepository->expects($this->once())->method('get')->willReturn($this->group);
        $groupRepository->expects($this->once())->method('add');

        $this->deleteFollowHandler = new DeleteFollowHandler($groupRepository, new FakeClock());

        $this->deleteFollowHandler->handle($this->deleteFollowCommand);
    }
}
