<?php

namespace Tailgate\Test\Application\Command\Group;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Group\SubmitScoreForGroupCommand;
use Tailgate\Application\Command\Group\SubmitScoreForGroupHandler;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\ScoreId;
use Tailgate\Domain\Model\Group\ScoreSubmitted;
use Tailgate\Domain\Model\Game\GameId;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Infrastructure\Persistence\EventStore\InMemory\InMemoryEventStore;
use Tailgate\Infrastructure\Persistence\Projection\InMemory\InMemoryGroupProjectionViewRepository;
use Tailgate\Infrastructure\Persistence\Repository\GroupRepository;

class SubmitScoreForGroupHandlerTest extends TestCase
{
    private $groupRepository;
    private $submitScoreForGroupCommand;
    private $submitScoreForGroupHandler;

    public function setUp()
    {
        $groupId = 'groupId';
        $userId = 'userId';
        $gameId = 'gameId';
        $homeTeamPrediction = '70';
        $awayTeamPrediction = '60';

        $group = Group::create(GroupId::fromString($groupId), 'groupName', UserId::fromString($userId));
        $group->clearRecordedEvents();

        $this->submitScoreForGroupCommand = new SubmitScoreForGroupCommand(
            $groupId,
            $userId,
            $gameId,
            $homeTeamPrediction,
            $awayTeamPrediction
        );

        $this->groupRepository = $this->getMockBuilder(GroupRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'add'])
            ->getMock();

        $this->groupRepository
           ->expects($this->once())
           ->method('get')
           ->willReturn($group);

        $this->groupRepository
            ->expects($this->once())
            ->method('add')
            ->with($this->callback(function($group) use (
                $groupId,
                $userId,
                $gameId,
                $homeTeamPrediction,
                $awayTeamPrediction
            ) {
                $events = $group->getRecordedEvents();

                return $events[0] instanceof ScoreSubmitted
                && $events[0]->getAggregateId()->equals(GroupId::fromString($groupId))
                && $events[0]->getScoreId() instanceof ScoreId
                && $events[0]->getUserId()->equals(UserId::fromString($userId))
                && $events[0]->getGameId()->equals(GameId::fromString($gameId))
                && $events[0]->getHomeTeamPrediction() == $homeTeamPrediction
                && $events[0]->getAwayTeamPrediction() == $awayTeamPrediction
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));
        
        $this->submitScoreForGroupHandler = new SubmitScoreForGroupHandler(
            $this->groupRepository
        );
    }

    public function testItAttemptsToAddANewGroupScoreToAGroup()
    {
        $this->submitScoreForGroupHandler->handle($this->submitScoreForGroupCommand);
    }
}
