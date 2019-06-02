<?php

namespace Tailgate\Application\Command\Follower;

use Tailgate\Domain\Model\Follower\Follower;
use Tailgate\Domain\Model\Follower\FollowerRepositoryInterface;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Team\TeamId;

class FollowTeamHandler
{
    public $followerRepository;

    public function __construct(FollowerRepositoryInterface $followerRepository)
    {
        $this->followerRepository = $followerRepository;
    }

    public function handle(FollowTeamCommand $followTeamCommand)
    {
        $groupId = $followTeamCommand->getGroupId();
        $teamId = $followTeamCommand->getTeamId();

        // confirm that team and group exists

        $follower = Follower::create(
            $this->followerRepository->nextIdentity(),
            GroupId::fromString($groupId),
            TeamId::fromString($teamId)
        );
        
        $this->followerRepository->add($follower);
    }
}