<?php

namespace Tailgate\Infrastructure\Persistence\Projection;

use Tailgate\Domain\Model\Group\FollowDeleted;
use Tailgate\Domain\Model\Group\GroupCreated;
use Tailgate\Domain\Model\Group\GroupDeleted;
use Tailgate\Domain\Model\Group\GroupScoreUpdated;
use Tailgate\Domain\Model\Group\GroupUpdated;
use Tailgate\Domain\Model\Group\MemberAdded;
use Tailgate\Domain\Model\Group\MemberDeleted;
use Tailgate\Domain\Model\Group\MemberUpdated;
use Tailgate\Domain\Model\Group\PlayerAdded;
use Tailgate\Domain\Model\Group\PlayerDeleted;
use Tailgate\Domain\Model\Group\PlayerOwnerChanged;
use Tailgate\Domain\Model\Group\ScoreDeleted;
use Tailgate\Domain\Model\Group\ScoreSubmitted;
use Tailgate\Domain\Model\Group\TeamFollowed;
use Tailgate\Infrastructure\Persistence\Projection\ProjectionInterface;

interface GroupProjectionInterface extends ProjectionInterface
{
    public function projectGroupCreated(GroupCreated $event);
    public function projectMemberAdded(MemberAdded $event);
    public function projectScoreSubmitted(ScoreSubmitted $event);
    public function projectGroupUpdated(GroupUpdated $event);
    public function projectMemberDeleted(MemberDeleted $event);
    public function projectScoreDeleted(ScoreDeleted $event);
    public function projectGroupScoreUpdated(GroupScoreUpdated $event);
    public function projectGroupDeleted(GroupDeleted $event);
    public function projectPlayerAdded(PlayerAdded $event);
    public function projectPlayerDeleted(PlayerDeleted $event);
    public function projectMemberUpdated(MemberUpdated $event);
    public function projectTeamFollowed(TeamFollowed $event);
    public function projectFollowDeleted(FollowDeleted $event);
    public function projectPlayerOwnerChanged(PlayerOwnerChanged $event);
}
