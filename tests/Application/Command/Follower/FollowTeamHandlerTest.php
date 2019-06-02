<?php

namespace Tailgate\Test\Application\Command\Follower;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Follower\FollowTeamCommand;
use Tailgate\Application\Command\Follower\FollowTeamHandler;
use Tailgate\Domain\Model\Follower\Follower;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Infrastructure\Persistence\Repository\FollowerRepository;

class FollowTeamHandlerTest extends TestCase
{
    private $followerRepository;
    private $followTeamCommand;
    private $followTeamHandler;

    public function setUp()
    {
        $groupId = 'groupId';
        $teamId = 'teamId';

        $this->followTeamCommand = new FollowTeamCommand(
            $groupId,
            $teamId
        );

        $this->followerRepository = $this->getMockBuilder(FollowerRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['add'])
            ->getMock();

        $this->followerRepository
            ->expects($this->once())
            ->method('add')
            ->with($this->callback(function($follower) use (
                $groupId,
                $teamId
            ) {
                return $follower instanceof Follower
                && $follower->getGroupId() === $groupId
                && $follower->getTeamId() === $teamId;
            }
        ));

        $this->followTeamHandler = new FollowTeamHandler(
            $this->followerRepository
        );
    }

    public function testItAttemptsToAddAFollowerToTheFollowerRepository()
    {
        $this->followTeamHandler->handle($this->followTeamCommand);
    }
}
