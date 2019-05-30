<?php

namespace Tailgate\Test\Application\Command\Group;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Group\CreateGroupCommand;
use Tailgate\Application\Command\Group\CreateGroupHandler;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Infrastructure\Persistence\Repository\GroupRepository;

class CreateGroupHandlerTest extends TestCase
{
    private $groupRepository;
    private $createGroupCommand;
    private $CreateGroupHandler;

    public function setUp()
    {
        $name = 'groupName';
        $ownerId = new UserId('ownerId');

        $this->createGroupCommand = new CreateGroupCommand(
            $name,
            $ownerId
        );

        $this->groupRepository = $this->getMockBuilder(GroupRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['add'])
            ->getMock();

         $this->groupRepository
            ->expects($this->once())
            ->method('add')
            ->with($this->callback(function($group) use (
                $name,
                $ownerId
            ) {
                return $group instanceof Group
                && $group->getName() === $name
                && $group->getOwnerId() === (string) $ownerId;
            }
        ));

        $this->createGroupHandler = new CreateGroupHandler(
            $this->groupRepository
        );
    }

    public function testItAttemptsToAddANewGroupToTheGroupRepository()
    {
        $this->createGroupHandler->handle($this->createGroupCommand);
    }
}
