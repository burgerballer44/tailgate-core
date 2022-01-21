<?php

namespace Tailgate\Test\Domain\Service\Group;

use Tailgate\Application\Command\Group\ChangePlayerOwnerCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupInviteCode;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Domain\Model\Group\PlayerId;
use Tailgate\Domain\Model\Group\PlayerOwnerChanged;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Service\Clock\FakeClock;
use Tailgate\Domain\Service\Group\ChangePlayerOwnerHandler;
use Tailgate\Test\BaseTestCase;

class ChangePlayerOwnerHandlerTest extends BaseTestCase
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
        $this->group->addMember($this->userId2, $this->dateOccurred);
        $this->ownerMemberId = $this->group->getMembers()[0]->getMemberId();
        $this->newMemberId = (string) $this->group->getMembers()[1]->getMemberId();
        $this->group->addPlayer($this->ownerMemberId, 'username', $this->dateOccurred);
        $this->group->clearRecordedEvents();

        $this->playerId = (string) $this->group->getPlayers()[0]->getPlayerId();

        $this->changePlayerOwnerCommand = new ChangePlayerOwnerCommand(
            $this->groupId,
            $this->playerId,
            $this->newMemberId
        );
    }

    public function testItAddsAPlayerOwnerChangedEventToAGroupInTheGroupRepository()
    {
        $groupRepository = $this->getMockBuilder(GroupRepositoryInterface::class)->getMock();
        $groupRepository->expects($this->once())->method('get')->willReturn($this->group);
        $groupRepository->expects($this->once())->method('add');

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())->method('assert')->willReturn(true);
        
        $changeplayerOwnerHandler = new ChangePlayerOwnerHandler($validator, new FakeClock(), $groupRepository);

        $changeplayerOwnerHandler->handle($this->changePlayerOwnerCommand);
    }
}
