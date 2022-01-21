<?php

namespace Tailgate\Test\Domain\Service\Group;

use Tailgate\Application\Command\Group\DeleteGroupCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupDeleted;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupInviteCode;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Service\Clock\FakeClock;
use Tailgate\Domain\Service\Group\DeleteGroupHandler;
use Tailgate\Test\BaseTestCase;

class DeleteGroupHandlerTest extends BaseTestCase
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

        $this->deleteGroupCommand = new DeleteGroupCommand(
            $this->groupId
        );
    }

    public function testItAddsAGroupDeletedEventToTheGroupRepository()
    {
        $groupRepository = $this->getMockBuilder(GroupRepositoryInterface::class)->getMock();
        $groupRepository->expects($this->once())->method('get')->willReturn($this->group);
        $groupRepository->expects($this->once())->method('add');

        $deleteGroupHandler = new DeleteGroupHandler($groupRepository, new FakeClock());

        $deleteGroupHandler->handle($this->deleteGroupCommand);
    }
}
