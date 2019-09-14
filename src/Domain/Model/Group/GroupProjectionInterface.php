<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Common\Projection\ProjectionInterface;

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
}
