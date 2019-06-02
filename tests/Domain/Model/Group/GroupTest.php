<?php

namespace Tailgate\Test\Domain\Model\User;

use Buttercup\Protects\AggregateHistory;
use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupCreated;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\ScoreId;
use Tailgate\Domain\Model\Group\ScoreSubmitted;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\Game\GameId;

class GroupTest extends TestCase
{
    private $id;
    private $name;
    private $ownerId;

    public function setUp()
    {
        $this->id = new GroupId('groupId');
        $this->name = 'groupName';
        $this->ownerId = new UserId('ownerId');
    }

    public function testGroupShouldBeTheSameAfterReconstitution()
    {
        $group = Group::create($this->id, $this->name, $this->ownerId);
        $events = $group->getRecordedEvents();
        $group->clearRecordedEvents();

        $reconstitutedUser = Group::reconstituteFrom(
            new AggregateHistory($this->id, (array) $events)
        );

        $this->assertEquals($group, $reconstitutedUser,
            'the reconstituted group does not match the original group');
    }

    public function testGroupCreatedEventOccursWhenGroupIsCreated()
    {
        $group = Group::create($this->id, $this->name, $this->ownerId);
        $events = $group->getRecordedEvents();
        $group->clearRecordedEvents();

        $this->assertCount(1, $events);
        $this->assertTrue($events[0] instanceof GroupCreated);
        $this->assertTrue($events[0]->getAggregateId()->equals($this->id));
        $this->assertEquals($this->name, $events[0]->getName());
        $this->assertEquals($this->ownerId, $events[0]->getOwnerId());
        $this->assertTrue($events[0]->getOccurredOn() instanceof \DateTimeImmutable);

        $this->assertCount(0, $group->getRecordedEvents());
    }

    public function testScoreSubmittedEventOccursWhenScoreIsAdded()
    {
        $group = Group::create($this->id, $this->name, $this->ownerId);
        // clear events because we only want to track the score submitted event
        $group->clearRecordedEvents();

        $userId = new UserId('userID');
        $gameId = new GameId('gameID');
        $homeTeamPrediction = '70';
        $awayTeamPrediction = '60';
        $group->submitScore($this->id, $userId, $gameId, $homeTeamPrediction, $awayTeamPrediction);
        $events = $group->getRecordedEvents();
        $group->clearRecordedEvents();

        $this->assertCount(1, $events);
        $this->assertTrue($events[0] instanceof ScoreSubmitted);
        $this->assertTrue($events[0]->getAggregateId()->equals($this->id));
        $this->assertTrue($events[0]->getScoreId() instanceof ScoreId);
        $this->assertTrue($events[0]->getUserId()->equals($userId));
        $this->assertTrue($events[0]->getGameId()->equals($gameId));
        $this->assertTrue($events[0]->getHomeTeamPrediction() == $homeTeamPrediction);
        $this->assertTrue($events[0]->getAwayTeamPrediction() == $awayTeamPrediction);
        $this->assertTrue($events[0]->getOccurredOn() instanceof \DateTimeImmutable);

        $this->assertCount(0, $group->getRecordedEvents());
    }
}
