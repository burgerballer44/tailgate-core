<?php

namespace Tailgate\Test\Application\Command\Season;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Query\Season\AllSeasonsQuery;
use Tailgate\Application\Query\Season\AllSeasonsQueryHandler;
use Tailgate\Domain\Model\Season\SeasonViewRepositoryInterface;
use Tailgate\Application\DataTransformer\SeasonDataTransformerInterface;

class AllSeasonsQueryHandlerTest extends TestCase
{
    public function testItAttemptsToGetAllSeasonsFromSeasonViewRepository()
    {
        $seasonViewRepository = $this->createMock(SeasonViewRepositoryInterface::class);
        $seasonViewTransformer = $this->createMock(SeasonDataTransformerInterface::class);
        $seasonViewRepository->expects($this->once())->method('all')->willReturn([]);

        $allSeasonsQuery = new AllSeasonsQuery();
        $allSeasonsQueryHandler = new AllSeasonsQueryHandler($seasonViewRepository, $seasonViewTransformer);
        $allSeasonsQueryHandler->handle($allSeasonsQuery);
    }
}
