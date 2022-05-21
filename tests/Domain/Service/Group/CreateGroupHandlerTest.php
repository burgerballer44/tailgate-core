<?php

namespace Tailgate\Test\Domain\Service\Group;

use Tailgate\Application\Command\Group\CreateGroupCommand;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Service\Clock\FakeClock;
use Tailgate\Domain\Service\Group\CreateGroupHandler;
use Tailgate\Test\BaseTestCase;

class CreateGroupHandlerTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->ownerId = UserId::fromString('userId');
        $this->groupName = 'groupName';

        $this->createGroupCommand = new CreateGroupCommand(
            $this->groupName,
            $this->ownerId
        );
    }

    public function testItAddsAGroupCreatedEventToTheGroupRepository()
    {
        $groupRepository = $this->getMockBuilder(GroupRepositoryInterface::class)->getMock();
        $groupRepository->expects($this->once())->method('nextIdentity')->willReturn(new GroupId());
        $groupRepository->expects($this->once())->method('add');

        $createGroupHandler = new CreateGroupHandler(new FakeClock(), $groupRepository);

        $createGroupHandler->handle($this->createGroupCommand);
    }
}
