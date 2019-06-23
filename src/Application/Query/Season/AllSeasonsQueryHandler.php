<?php

namespace Tailgate\Application\Query\Season;

use Tailgate\Domain\Model\Season\SeasonViewRepositoryInterface;

class AllSeasonsQueryHandler
{
    private $seasonViewRepository;

    public function __construct(SeasonViewRepositoryInterface $seasonViewRepository)
    {
        $this->seasonViewRepository = $seasonViewRepository;
    }

    public function handle(AllSeasonsQuery $allSeasonsQuery)
    {
        return $this->seasonViewRepository->all();
    }
}