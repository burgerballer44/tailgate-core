<?php

namespace Tailgate\Test\Domain\Service\Group;

use Tailgate\Application\Command\Group\DeletePlayerCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupInviteCode;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Service\Group\DeletePlayerHandler;
use Tailgate\Infrastructure\Service\Clock\FakeClock;
use Tailgate\Test\BaseTestCase;

class DeletePlayerHandlerTest extends BaseTestCase
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
        $memberId = $this->group->getMembers()[0]->getMemberId();
        $this->group->addPlayer($memberId, 'username', $this->dateOccurred);
        $this->playerId = (string) $this->group->getPlayers()[0]->getPlayerId();

        $this->group->clearRecordedEvents();

        $this->deletePlayerCommand = new DeletePlayerCommand(
            $this->groupId,
            $this->playerId
        );
    }

    public function testItAddsAPlayerDeletedEventToTheGroupRepository()
    {
        $groupRepository = $this->getMockBuilder(GroupRepositoryInterface::class)->getMock();
        $groupRepository->expects($this->once())->method('get')->willReturn($this->group);
        $groupRepository->expects($this->once())->method('add');

        $deletePlayerHandler = new DeletePlayerHandler($groupRepository, new FakeClock());

        $deletePlayerHandler->handle($this->deletePlayerCommand);
    }
}
