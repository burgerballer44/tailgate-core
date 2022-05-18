<?php

namespace Tailgate\Test\Domain\Service\Group;

use Tailgate\Application\Command\Group\AddPlayerToGroupCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupInviteCode;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Service\Clock\FakeClock;
use Tailgate\Domain\Service\Group\AddPlayerToGroupHandler;
use Tailgate\Test\BaseTestCase;

class AddPlayerToGroupHandlerTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->groupId = GroupId::fromString('groupId');
        $this->userId = UserId::fromString('userId');
        $this->groupName = 'groupName';
        $this->groupInviteCode = GroupInviteCode::create();
        $this->username = 'username';
        $this->dateOccurred = Date::fromDateTimeImmutable($this->getFakeTime()->currentTime());

        // create a group and clear events
        $this->group = Group::create(
            $this->groupId,
            $this->groupName,
            $this->groupInviteCode,
            $this->userId,
            $this->dateOccurred
        );
        $this->memberId = (string) $this->group->getMembers()[0]->getMemberId();
        $this->group->clearRecordedEvents();

        $this->addPlayerToGroupCommand = new AddPlayerToGroupCommand(
            $this->groupId,
            $this->memberId,
            $this->username,
            $this->dateOccurred
        );
    }

    public function testItAddsAPlayerAddedEventToAGroupInTheGroupRepository()
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())->method('assert')->willReturn(true);

        $groupRepository = $this->getMockBuilder(GroupRepositoryInterface::class)->getMock();
        $groupRepository->expects($this->once())->method('get')->willReturn($this->group);
        $groupRepository->expects($this->once())->method('add');

        $addPlayerToGroupHandler = new AddPlayerToGroupHandler($validator, new FakeClock(), $groupRepository);

        $addPlayerToGroupHandler->handle($this->addPlayerToGroupCommand);
    }
}
