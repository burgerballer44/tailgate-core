<?php

namespace Tailgate\Test\Domain\Service\Group;

use Tailgate\Application\Command\Group\UpdateGroupCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupInviteCode;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Service\Group\UpdateGroupHandler;
use Tailgate\Infrastructure\Service\Clock\FakeClock;
use Tailgate\Test\BaseTestCase;

class UpdateGroupHandlerTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->groupId = GroupId::fromString('groupId');
        $this->userId = UserId::fromString('userId');
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

        $this->updateGroupCommand = new UpdateGroupCommand(
            GroupId::fromString($this->groupId),
            $this->groupName,
            $this->userId
        );
    }

    public function testItAddsAGroupUpdatedEventToTheGroupRepository()
    {
        $groupRepository = $this->getMockBuilder(GroupRepositoryInterface::class)->getMock();
        $groupRepository->expects($this->once())->method('get')->willReturn($this->group);
        $groupRepository->expects($this->once())->method('add');

        $updateGroupHandler = new UpdateGroupHandler(new FakeClock(), $groupRepository);

        $updateGroupHandler->handle($this->updateGroupCommand);
    }
}
