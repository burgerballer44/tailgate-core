<?php

namespace Tailgate\Test\Domain\Service\Group;

use Tailgate\Application\Command\Group\UpdateMemberCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupInviteCode;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Group\GroupRole;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Domain\Model\Group\MemberUpdated;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Service\Clock\FakeClock;
use Tailgate\Domain\Service\Group\UpdateMemberHandler;
use Tailgate\Test\BaseTestCase;

class UpdateMemberHandlerTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->groupId = GroupId::fromString('groupId');
        $this->userId = UserId::fromString('userId');
        $this->groupName = 'groupName';
        $this->groupInviteCode = GroupInviteCode::create();
        $this->dateOccurred = Date::fromDateTimeImmutable($this->getFakeTime()->currentTime());
        $this->groupRole = GroupRole::getGroupAdmin();
        $this->allowMultiplePlayers = false;

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

        $this->updateMemberCommand = new UpdateMemberCommand(
            $this->groupId,
            $this->memberId,
            $this->groupRole,
            $this->allowMultiplePlayers
        );
    }

    public function testItAddsAMemberUpdatedEventToAGroupInTheGroupRepository()
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())->method('assert')->willReturn(true);

        $groupRepository = $this->getMockBuilder(GroupRepositoryInterface::class)->getMock();
        $groupRepository->expects($this->once())->method('get')->willReturn($this->group);
        $groupRepository->expects($this->once())->method('add');

        $updateMemberHandler = new UpdateMemberHandler($validator, new FakeClock(), $groupRepository);

        $updateMemberHandler->handle($this->updateMemberCommand);
    }
}
