<?php

namespace Tailgate\Test\Domain\Service\Group;

use Tailgate\Application\Command\Group\AddMemberToGroupCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupInviteCode;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Service\Clock\FakeClock;
use Tailgate\Domain\Service\Group\AddMemberToGroupHandler;
use Tailgate\Test\BaseTestCase;

class AddMemberToGroupHandlerTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->groupId = GroupId::fromString('groupId');
        $this->userId = UserId::fromString('userId');
        $this->userId2 = UserId::fromString('userId2');
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

        $this->addMemberToGroupCommand = new AddMemberToGroupCommand(
            $this->groupId,
            $this->userId2
        );
    }

    public function testItAddsAMemberAddedEventToAGroupInTheGroupRepository()
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())->method('assert')->willReturn(true);

        $groupRepository = $this->getMockBuilder(GroupRepositoryInterface::class)->getMock();
        $groupRepository->expects($this->once())->method('get')->willReturn($this->group);
        $groupRepository->expects($this->once())->method('add');

        $addMemberToGroupHandler = new AddMemberToGroupHandler($validator, new FakeClock(), $groupRepository);

        $addMemberToGroupHandler->handle($this->addMemberToGroupCommand);
    }
}
