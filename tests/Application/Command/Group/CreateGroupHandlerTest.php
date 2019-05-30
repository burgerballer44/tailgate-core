<?php

namespace Tailgate\Test\Application\Command\Group;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Group\CreateGroupCommand;
use Tailgate\Application\Command\Group\CreateGroupHandler;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Infrastructure\Persistence\Repository\GroupRepository;

class CreateGroupHandlerTest extends TestCase
{
    private $name = 'groupName';
    private $groupRepository;
    private $createGroupCommand;
    private $CreateGroupHandler;

    public function setUp()
    {
        $this->createGroupCommand = new CreateGroupCommand(
            $this->name
        );

        $this->groupRepository = $this->getMockBuilder(GroupRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['add'])
            ->getMock();

         $this->groupRepository
            ->expects($this->once())
            ->method('add')
            ->with($this->callback(function($group) {
                return $group instanceof Group;
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
