<?php

namespace Tailgate\Test\Domain\Service\Season;

use Tailgate\Test\BaseTestCase;
use Tailgate\Application\Query\Season\AllSeasonsQuery;
use Tailgate\Domain\Service\Season\AllSeasonsQueryHandler;
use Tailgate\Domain\Model\Season\SeasonViewRepositoryInterface;
use Tailgate\Domain\Service\DataTransformer\SeasonDataTransformerInterface;

class AllSeasonsQueryHandlerTest extends BaseTestCase
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
