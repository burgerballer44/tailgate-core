<?php

namespace Tailgate\Test\Application\Command\Season;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Query\Season\SeasonQuery;
use Tailgate\Application\Query\Season\SeasonQueryHandler;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonView;
use Tailgate\Domain\Model\Season\SeasonViewRepositoryInterface;
use Tailgate\Application\DataTransformer\SeasonDataTransformerInterface;

class SeasonQueryHandlerTest extends TestCase
{
    public function testItAttemptsToGetASeasonBySeasonIdFromTeamViewRepository()
    {
        $seasonId = 'seasonId';

        $seasonViewRepository = $this->createMock(SeasonViewRepositoryInterface::class);
        $seasonViewTransformer = $this->createMock(SeasonDataTransformerInterface::class);
        $seasonView = $this->createMock(SeasonView::class);
        $seasonViewRepository->expects($this->once())
            ->method('get')
            ->willReturn($seasonView)
            ->with($this->callback(function($seasonQuerySeasonId) use ($seasonId) {
                return (new SeasonId($seasonId))->equals($seasonQuerySeasonId);
            }));

        $seasonQuery = new SeasonQuery($seasonId);
        $seasonQueryHandler = new SeasonQueryHandler($seasonViewRepository, $seasonViewTransformer);
        $seasonQueryHandler->handle($seasonQuery);
    }
}
