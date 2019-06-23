<?php

namespace Tailgate\Test\Application\Command\Season;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Query\Season\AllSeasonsQuery;
use Tailgate\Application\Query\Season\AllSeasonsQueryHandler;
use Tailgate\Domain\Model\Season\SeasonViewRepositoryInterface;

class AllSeasonsQueryHandlerTest extends TestCase
{
    public function testItAttemptsToGetAllSeasonsFromSeasonViewRepository()
    {
        $seasonViewRepository = $this->createMock(SeasonViewRepositoryInterface::class);
        $seasonViewRepository->expects($this->once())->method('all');

        $allSeasonsQuery = new AllSeasonsQuery();
        $allSeasonsQueryHandler = new AllSeasonsQueryHandler($seasonViewRepository);
        $allSeasonsQueryHandler->handle($allSeasonsQuery);
    }
}
